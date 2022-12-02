<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn;

use InvalidArgumentException;
use Throwable;
use Two_Factor_Provider;
use TwoFactor_Provider_WebAuthn;
use UnexpectedValueException;
use WP_User;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\{
	MadWizard\WebAuthn\Credential\CredentialId,
	MadWizard\WebAuthn\Extension\AppId\AppIdExtensionInput,
	MadWizard\WebAuthn\Json\JsonConverter,
	MadWizard\WebAuthn\Server\Authentication\AuthenticationContext,
	MadWizard\WebAuthn\Server\Authentication\AuthenticationOptions,
};

class WebAuthn_Provider extends Two_Factor_Provider {
	public const AUTHENTICATION_CONTEXT_USER_META = Constants::AUTHENTICATION_CONTEXT_USER_META_KEY;

	/** @var static|null */
	private static $instance = null;

	/**
	 * @return static
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new static();
		}

		return self::$instance;
	}

	final protected function __construct() {
		add_action( 'two_factor_user_options_TwoFactor_Provider_WebAuthn', [ $this, 'user_options' ] );
		parent::__construct();

		add_filter( 'load_script_translation_file', [ $this, 'load_script_translation_file' ], 10, 3 );
		add_filter( 'two_factor_enabled_providers_for_user', [ $this, 'two_factor_enabled_providers_for_user' ] );
		add_filter( 'two_factor_primary_provider_for_user', [ $this, 'two_factor_primary_provider_for_user' ] );
	}

	/**
	 * @return string
	 */
	public function get_label() {
		return _x( 'WebAuthn', 'Provider label', 'two-factor-provider-webauthn' );
	}

	/**
	 * @param WP_User $user
	 * @return void
	 */
	public function authentication_page( $user ) {
		require_once ABSPATH . '/wp-admin/includes/template.php';

		if ( ! is_ssl() ) {
			printf( '<p>%s</p>', esc_html__( 'WebAuthn requires an HTTPS connection. Please use an alternative second factor method.', 'two-factor-provider-webauthn' ) );
			return;
		}

		wp_enqueue_script(
			'webauthn-login',
			plugins_url( 'assets/login.min.js', __DIR__ ),
			[ 'wp-i18n' ],
			(string) filemtime( __DIR__ . '/../assets/login.min.js' ),
			true
		);

		$settings     = Settings::instance();
		$auth_options = AuthenticationOptions::createForUser( WebAuthn_User::get_for( $user )->getUserHandle() );
		$auth_options->setUserVerification( $settings->get_user_verification_requirement() );
		$auth_options->addExtensionInput( new AppIdExtensionInput( Utils::get_u2f_app_id() ) );
		if ( $settings->get_timeout() ) {
			$auth_options->setTimeout( $settings->get_timeout() * 1000 );
		}

		$server  = Utils::create_webauthn_server();
		$request = $server->startAuthentication( $auth_options );
		$context = $request->getContext();
		$options = $request->getClientOptionsJson();

		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize -- webauthn-server insists on serialize() :-(
		update_user_meta( $user->ID, self::AUTHENTICATION_CONTEXT_USER_META, base64_encode( serialize( $context ) ) );

		wp_localize_script( 'webauthn-login', 'tfa_webauthn', [
			'options' => $options,
		] );

		wp_set_script_translations( 'webauthn-login', 'two-factor-provider-webauthn', plugin_dir_path( dirname( __DIR__ ) . '/index.php' ) . 'lang' );

		Utils::render( 'login' );
	}

	/**
	 * @param WP_User $user
	 * @return bool
	 */
	public function validate_authentication( $user ) {
		try {
			$server  = Utils::create_webauthn_server();
			$context = (string) get_user_meta( $user->ID, self::AUTHENTICATION_CONTEXT_USER_META, true );
			/** @var mixed */
			$context = unserialize( base64_decode( $context ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize -- the value was stored serialize()'d
			if ( ! ( $context instanceof AuthenticationContext ) ) {
				throw new UnexpectedValueException( __( 'Unable to retrieve the authentication context.', 'two-factor-provider-webauthn' ) );
			}

			// We cannot use WordPress sanitization functions here: the response from webauthn must not be altered.
			// We validate that `webauthn_response` is a string, valid JSON, and decodes to an object (associative array in terms of PHP).
			// If any of the conditions does not hold, we fail the request.
			// The webauthn-server library performs further validation in accordance with the specification.
			// Nonce is validated by the Two Factor plugin.
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Missing
			$response = $_POST['webauthn_response'] ?? null;    // Dangerous to sanitize; the code will validate the value
			if ( ! is_string( $response ) ) {
				throw new InvalidArgumentException( __( 'Bad request.', 'two-factor-provider-webauthn' ) );
			}

			/** @var mixed $credential */
			$credential = json_decode( wp_unslash( $response ), true, 512, JSON_THROW_ON_ERROR );

			if ( is_array( $credential ) ) {
				$settings = Settings::instance();
				$repo     = new WebAuthn_Credential_Store();

				// Chrome on Android requires some workarounds :-(
				if ( $settings->get_u2f_hack() ) {
					$credential = $this->apply_u2f_hack( $repo, $credential );
				}

				$result = $server->finishAuthentication(
					JsonConverter::decodeCredential( $credential, 'assertion' ),
					$context
				);

				$repo->update_last_used_date( $result->getUserCredential()->getCredentialId(), time() );
			}

			return true;
		} catch ( Throwable $e ) {
			return false;
		} finally {
			delete_user_meta( $user->ID, self::AUTHENTICATION_CONTEXT_USER_META );
		}
	}

	/**
	 * @param WP_User $user
	 * @return bool
	 */
	public function is_available_for_user( $user ) {
		return ! empty( WebAuthn_Credential_Store::get_user_keys( $user ) );
	}

	public function user_options(): void {
		Utils::render( 'user-options' );
	}

	/**
	 * Filters the file path for loading script translations for the given script handle and text domain.
	 *
	 * @param string|false $file   Path to the translation file to load. False if there isn't one.
	 * @param string       $handle Name of the script to register a translation domain to.
	 * @param string       $domain The text domain.
	 * @return string|false
	 */
	public function load_script_translation_file( $file, $handle, $domain ) {
		if ( is_string( $file ) && 'two-factor-provider-webauthn' === $domain ) {
			$fname = basename( $file );
			$dname = dirname( $file );
			$fname = str_replace( "-{$handle}", '', $fname );
			$fname = str_replace( $domain, "{$domain}-js", $fname );
			$file  = $dname . DIRECTORY_SEPARATOR . $fname;
		}

		return $file;
	}

	private function apply_u2f_hack( WebAuthn_Credential_Store $repo, array $credential ): array {
		if ( isset( $credential['id'] ) && is_string( $credential['id'] ) && isset( $credential['clientExtensionResults']['appid'] ) && false === $credential['clientExtensionResults']['appid'] ) {
			$cid  = CredentialId::fromString( $credential['id'] );
			$cred = $repo->get_credential_by_id( $cid );
			if ( $cred && ! empty( $cred->u2f ) ) {
				$credential['clientExtensionResults']['appid'] = true;
			}
		}

		return $credential;
	}

	/**
	 * Filter the enabled two-factor authentication providers for this user.
	 *
	 * @psalm-param class-string[] $enabled_providers
	 * @psalm-return class-string[]
	 */
	public function two_factor_enabled_providers_for_user( array $enabled_providers ): array {
		if ( in_array( \Two_Factor_FIDO_U2F::class, $enabled_providers, true ) ) {
			$enabled_providers[] = TwoFactor_Provider_WebAuthn::class;
		}

		return $enabled_providers;
	}

	/**
	 * Filter the two-factor authentication provider used for this user.
	 *
	 * @param string $provider
	 * @psalm-param class-string $provider
	 * @return string
	 * @psalm-return class-string
	 */
	public function two_factor_primary_provider_for_user( $provider ) {
		if ( \Two_Factor_FIDO_U2F::class === $provider ) {
			$provider = TwoFactor_Provider_WebAuthn::class;
		}

		return $provider;
	}
}
