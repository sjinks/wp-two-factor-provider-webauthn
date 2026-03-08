<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn;

use InvalidArgumentException;
use Throwable;
use Two_Factor_Core;
use UnexpectedValueException;
use WildWolf\Utils\Singleton;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\{
	MadWizard\WebAuthn\Dom\ResidentKeyRequirement,
	MadWizard\WebAuthn\Exception\WebAuthnException,
	MadWizard\WebAuthn\Json\JsonConverter,
	MadWizard\WebAuthn\Server\Registration\RegistrationContext,
	MadWizard\WebAuthn\Server\Registration\RegistrationOptions,
	MadWizard\WebAuthn\Server\Registration\RegistrationResultInterface,
};

final class AJAX {
	use Singleton;

	public const REGISTRATION_CONTEXT_USER_META = Constants::REGISTRATION_CONTEXT_USER_META_KEY;

	private function __construct() {
		$this->admin_init();
	}

	public function admin_init(): void {
		add_action( 'wp_ajax_webauthn_preregister', [ $this, 'wp_ajax_webauthn_preregister' ] );
		add_action( 'wp_ajax_webauthn_register', [ $this, 'wp_ajax_webauthn_register' ] );
		add_action( 'wp_ajax_webauthn_delete_key', [ $this, 'wp_ajax_webauthn_delete_key' ] );
		add_action( 'wp_ajax_webauthn_rename_key', [ $this, 'wp_ajax_webauthn_rename_key' ] );
	}

	private function verify_nonce( string $nonce ): void {
		if ( false === check_ajax_referer( $nonce, false, false ) ) {
			wp_send_json_error( __( 'The nonce has expired. Please reload the page and try again.', 'two-factor-provider-webauthn' ), 400 );
		}
	}

	private function check_registration_nonce( int $user_id ): void {
		$this->verify_nonce( "webauthn-register_key_{$user_id}" );
	}

	private function verify_capabilities( int $user_id ): void {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			wp_send_json_error( __( 'Bad request.', 'two-factor-provider-webauthn' ), 400 );
		}
	}

	public function wp_ajax_webauthn_preregister(): void {
		$user_id = (int) Utils::get_post_field_as_string( 'user_id' );

		$this->check_registration_nonce( $user_id );
		$this->verify_capabilities( $user_id );

		try {
			$user = get_user_by( 'id', $user_id );
			if ( false === $user ) {
				throw new InvalidArgumentException( __( 'Bad request.', 'two-factor-provider-webauthn' ) );
			}

			$server   = Utils::create_webauthn_server();
			$settings = Settings::instance();

			$reg_options = RegistrationOptions::createForUser( WebAuthn_User::get_for( $user ) );
			$reg_options->setExcludeExistingCredentials( true );
			$reg_options->setUserVerification( $settings->get_user_verification_requirement() );

			if ( $settings->get_authenticator_attachment() ) {
				$reg_options->setAuthenticatorAttachment( $settings->get_authenticator_attachment() );
			}

			if ( $settings->get_timeout() ) {
				$reg_options->setTimeout( $settings->get_timeout() * 1000 );
			}

			$options = $server->startRegistration( $reg_options );

			$context = $options->getContext();
			// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
			update_user_meta( $user_id, self::REGISTRATION_CONTEXT_USER_META, base64_encode( serialize( $context ) ) );

			$json = $options->getClientOptionsJson();

			/** @psalm-var array{authenticatorSelection?: array{residentKey?: string, requireResidentKey?: bool}} $json */
			$json['authenticatorSelection'] ??= [];

			$resident_key_requirement = $settings->get_resident_key_requirement();
			switch ( $resident_key_requirement ) {
				case 'preferred':
				default:
					$json['authenticatorSelection']['residentKey']        = 'preferred';
					$json['authenticatorSelection']['requireResidentKey'] = false;
					break;

				case ResidentKeyRequirement::DISCOURAGED:
					$json['authenticatorSelection']['residentKey']        = 'discouraged';
					$json['authenticatorSelection']['requireResidentKey'] = false;
					break;

				case ResidentKeyRequirement::REQUIRED:
					$json['authenticatorSelection']['residentKey']        = 'required';
					$json['authenticatorSelection']['requireResidentKey'] = true;
					break;
			}

			wp_send_json_success( [
				'options' => $json,
				'nonce'   => wp_create_nonce( "webauthn-register_key_{$user_id}" ),
			] );
		} catch ( WebAuthnException | InvalidArgumentException $e ) {
			/**
			 * Fires when an error occurs during the pre-registration process, which includes generating registration options and saving the registration context.
			 *
			 * @param Throwable $e The exception that caused the failure.
			 * @param int $user_id The ID of the user attempting to register a key.
			 * @since 2.6.0
			 */
			do_action( 'webauthn_preregistration_error', $e, $user_id );
			wp_send_json_error( $e->getMessage(), 400 );
		} catch ( Throwable $e ) {
			do_action( 'webauthn_preregistration_error', $e, $user_id );
			wp_send_json_error( __( 'An unexpected error occurred. Please try again later.', 'two-factor-provider-webauthn' ), 400 );
		}
	}

	/**
	 * @global wpdb $wpdb
	 */
	public function wp_ajax_webauthn_register(): void {
		$user_id = (int) Utils::get_post_field_as_string( 'user_id' );

		$this->check_registration_nonce( $user_id );
		$this->verify_capabilities( $user_id );

		/** @var RegistrationResultInterface|null $result */
		$result = null;

		try {
			$user = get_user_by( 'id', $user_id );
			if ( false === $user ) {
				throw new InvalidArgumentException( __( 'Bad request.', 'two-factor-provider-webauthn' ) );
			}

			$server  = Utils::create_webauthn_server();
			$context = (string) get_user_meta( $user_id, self::REGISTRATION_CONTEXT_USER_META, true );
			/** @var mixed */
			$context = unserialize( base64_decode( $context ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize
			if ( ! ( $context instanceof RegistrationContext ) ) {
				throw new UnexpectedValueException( __( 'Unable to retrieve the registration context.', 'two-factor-provider-webauthn' ) );
			}

			// We cannot use WordPress sanitization functions here: the credential must not be altered.
			// We validate that `credential` is a string, valid JSON, and decodes to an object (associative array in terms of PHP).
			// If any of the conditions does not hold, we fail the request.
			// The webauthn-server library performs further validation in accordance with the specification.
			// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$credential = $_POST['credential'] ?? null; // Dangerous to sanitize; the code will validate the value
			if ( ! is_string( $credential ) ) {
				throw new InvalidArgumentException( __( 'Bad request.', 'two-factor-provider-webauthn' ) );
			}

			/** @var mixed */
			$credential = json_decode( wp_unslash( $credential ), true, 512, JSON_THROW_ON_ERROR );
			if ( is_array( $credential ) ) {
				$result = $server->finishRegistration(
					JsonConverter::decodeCredential( $credential, 'attestation' ),
					$context
				);

				$name  = Utils::get_post_field_as_string( 'name' );
				$store = new WebAuthn_Credential_Store();
				$key   = $store->save_user_key( $name, $result );
				if ( null === $key ) {
					throw new UnexpectedValueException( __( 'Unable to save the key to the database.', 'two-factor-provider-webauthn' ) );
				}

				$suppress_output = (bool) apply_filters( 'webauthn_register_key_suppress_output', false, $user, $key );
				if ( ! $suppress_output ) {
					$table = new Key_Table( $user );
					ob_start();
					$table->single_row( (object) $key );
					$row = ob_get_clean();
				} else {
					$row = '';
				}

				Two_Factor_Core::update_current_user_session( [ 'two-factor-login' => time() ] );
				wp_send_json_success( [
					'row'   => $row,
					'nonce' => wp_create_nonce( "webauthn-register_key_{$user_id}" ),
				] );
			} else {
				throw new InvalidArgumentException( __( 'Bad request.', 'two-factor-provider-webauthn' ) );
			}
		} catch ( WebAuthnException | InvalidArgumentException | UnexpectedValueException $e ) {
			/**
			 * Fires when an error occurs during the registration process, which includes validating the credential and saving it to the database.
			 *
			 * @param Throwable $e The exception that caused the failure.
			 * @param int $user_id The ID of the user attempting to register a key.
			 * @param RegistrationResultInterface|null $result The result of the registration attempt, if available. This may be null if the error occurred before the registration result could be obtained.
			 * @since 2.6.0
			 */
			do_action( 'webauthn_registration_error', $e, $user_id, $result );
			wp_send_json_error( $e->getMessage(), 400 );
		} catch ( Throwable $e ) {
			do_action( 'webauthn_registration_error', $e, $user_id, $result );
			wp_send_json_error( __( 'An unexpected error occurred. Please try again later.', 'two-factor-provider-webauthn' ), 400 );
		} finally {
			delete_user_meta( $user_id, self::REGISTRATION_CONTEXT_USER_META );
		}
	}

	public function wp_ajax_webauthn_delete_key(): void {
		$user_id = (int) Utils::get_post_field_as_string( 'user_id' );
		$handle  = Utils::get_post_field_as_string( 'handle' );

		$this->verify_nonce( "delete-key_{$handle}" );
		$this->verify_capabilities( $user_id );

		$user = get_user_by( 'id', $user_id );
		if ( false === $user ) {
			wp_send_json_error( __( 'Bad request.', 'two-factor-provider-webauthn' ), 400 );
		}

		$store = new WebAuthn_Credential_Store();
		$store->delete_user_key( $user, $handle );
		Two_Factor_Core::update_current_user_session( [ 'two-factor-login' => time() ] );
		wp_send_json_success();
	}

	public function wp_ajax_webauthn_rename_key(): void {
		$user_id = (int) Utils::get_post_field_as_string( 'user_id' );
		$handle  = Utils::get_post_field_as_string( 'handle' );

		$this->verify_nonce( "rename-key_{$handle}" );
		$this->verify_capabilities( $user_id );

		$name = Utils::get_post_field_as_string( 'name' );
		if ( empty( $name ) ) {
			wp_send_json_error( __( 'Key name cannot be empty.', 'two-factor-provider-webauthn' ), 400 );
		}

		$user = get_user_by( 'id', $user_id );
		if ( false === $user ) {
			wp_send_json_error( __( 'Bad request.', 'two-factor-provider-webauthn' ), 400 );
		}

		$store   = new WebAuthn_Credential_Store();
		$success = $store->rename_key( $user, $handle, $name );
		if ( $success ) {
			Two_Factor_Core::update_current_user_session( [ 'two-factor-login' => time() ] );
			wp_send_json_success( [ 'name' => $name ] );
		}

		wp_send_json_error( __( 'Failed to rename the key.', 'two-factor-provider-webauthn' ), 400 );
	}
}
