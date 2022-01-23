<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn;

use MadWizard\WebAuthn\Credential\CredentialId;
use MadWizard\WebAuthn\Extension\AppId\AppIdExtensionInput;
use MadWizard\WebAuthn\Json\JsonConverter;
use MadWizard\WebAuthn\Server\Authentication\AuthenticationContext;
use MadWizard\WebAuthn\Server\Authentication\AuthenticationOptions;
use Throwable;
use Two_Factor_Provider;
use UnexpectedValueException;
use WP_User;

class WebAuthn_Provider extends Two_Factor_Provider {
	public const AUTHENTICATION_CONTEXT_USER_META = '_webauthn_auth_context';

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
	}

	/**
	 * @return string
	 */
	public function get_label() {
		return _x( 'WebAuthn', 'Provider label', '2fa-wa' );
	}

	/**
	 * @param WP_User $user
	 * @return void
	 */
	public function authentication_page( $user ) {
		/** @psalm-suppress UnresolvableInclude */
		require_once ABSPATH . '/wp-admin/includes/template.php';

		if ( ! is_ssl() ) {
			printf( '<p>%s</p>', esc_html__( 'WebAuthn requires an HTTPS connection. Please use an alternative second factor method.', '2fa-wa' ) );
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

		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
		update_user_meta( $user->ID, self::AUTHENTICATION_CONTEXT_USER_META, base64_encode( serialize( $context ) ) );

		wp_localize_script( 'webauthn-login', 'tfa_webauthn', [
			'options' => $options,
		] );

		wp_set_script_translations( 'webauthn-login', '2fa-wa-js', plugin_dir_path( dirname( __DIR__ ) . '/index.php' ) . '/lang' );

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
			$context = unserialize( base64_decode( $context ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize
			if ( ! ( $context instanceof AuthenticationContext ) ) {
				throw new UnexpectedValueException( __( 'Unable to retrieve the authentication context', '2fa-wa' ) );
			}

			/** @var mixed $credential */
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Missing
			$credential = json_decode( wp_unslash( (string) ( $_POST['webauthn_response'] ?? ' ' ) ), true, 512, JSON_THROW_ON_ERROR );

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
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( $e->getMessage() );
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
		if ( is_string( $file ) && '2fa-wa-js' === $domain ) {
			$file = str_replace( "-{$handle}", '', $file );
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
}
