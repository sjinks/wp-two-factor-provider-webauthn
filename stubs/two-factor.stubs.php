<?php

namespace {
    /**
     * A compatibility layer for some of the most popular plugins.
     *
     * @package Two_Factor
     */
    /**
     * A compatibility layer for some of the most popular plugins.
     *
     * Should be used with care because ideally we wouldn't need
     * any integration specific code for this plugin. Everything should
     * be handled through clever use of hooks and best practices.
     */
    class Two_Factor_Compat
    {
        /**
         * Initialize all the custom hooks as necessary.
         *
         * @return void
         */
        public function init()
        {
        }
        /**
         * Jetpack single sign-on wants long-lived sessions for users.
         *
         * @param boolean $rememberme Current state of the "remember me" toggle.
         *
         * @return boolean
         */
        public function jetpack_rememberme($rememberme)
        {
        }
        /**
         * Helper to detect the presence of the active SSO module.
         *
         * @return boolean
         */
        public function jetpack_is_sso_active()
        {
        }
    }
    /**
     * Two Factore Core Class.
     *
     * @package Two_Factor
     */
    /**
     * Class for creating two factor authorization.
     *
     * @since 0.1-dev
     *
     * @package Two_Factor
     */
    class Two_Factor_Core
    {
        /**
         * The user meta provider key.
         *
         * @type string
         */
        const PROVIDER_USER_META_KEY = '_two_factor_provider';
        /**
         * The user meta enabled providers key.
         *
         * @type string
         */
        const ENABLED_PROVIDERS_USER_META_KEY = '_two_factor_enabled_providers';
        /**
         * The user meta nonce key.
         *
         * @type string
         */
        const USER_META_NONCE_KEY = '_two_factor_nonce';
        /**
         * URL query paramater used for our custom actions.
         *
         * @var string
         */
        const USER_SETTINGS_ACTION_QUERY_VAR = 'two_factor_action';
        /**
         * Nonce key for user settings.
         *
         * @var string
         */
        const USER_SETTINGS_ACTION_NONCE_QUERY_ARG = '_two_factor_action_nonce';
        /**
         * Keep track of all the password-based authentication sessions that
         * need to invalidated before the second factor authentication.
         *
         * @var array
         */
        private static $password_auth_tokens = array();
        /**
         * Set up filters and actions.
         *
         * @param object $compat A compaitbility later for plugins.
         *
         * @since 0.1-dev
         */
        public static function add_hooks($compat)
        {
        }
        /**
         * Loads the plugin's text domain.
         *
         * Sites on WordPress 4.6+ benefit from just-in-time loading of translations.
         */
        public static function load_textdomain()
        {
        }
        /**
         * For each provider, include it and then instantiate it.
         *
         * @since 0.1-dev
         *
         * @return array
         */
        public static function get_providers()
        {
        }
        /**
         * Enable the dummy method only during debugging.
         *
         * @param array $methods List of enabled methods.
         *
         * @return array
         */
        public static function enable_dummy_method_for_debug($methods)
        {
        }
        /**
         * Check if the debug mode is enabled.
         *
         * @return boolean
         */
        protected static function is_wp_debug()
        {
        }
        /**
         * Get the user settings page URL.
         *
         * Fetch this from the plugin core after we introduce proper dependency injection
         * and get away from the singletons at the provider level (should be handled by core).
         *
         * @param integer $user_id User ID.
         *
         * @return string
         */
        protected static function get_user_settings_page_url($user_id)
        {
        }
        /**
         * Get the URL for resetting the secret token.
         *
         * @param integer $user_id User ID.
         * @param string  $action Custom two factor action key.
         *
         * @return string
         */
        public static function get_user_update_action_url($user_id, $action)
        {
        }
        /**
         * Check if a user action is valid.
         *
         * @param integer $user_id User ID.
         * @param string  $action User action ID.
         *
         * @return boolean
         */
        public static function is_valid_user_action($user_id, $action)
        {
        }
        /**
         * Get the ID of the user being edited.
         *
         * @return integer
         */
        public static function current_user_being_edited()
        {
        }
        /**
         * Trigger our custom update action if a valid
         * action request is detected and passes the nonce check.
         *
         * @return void
         */
        public static function trigger_user_settings_action()
        {
        }
        /**
         * Keep track of all the authentication cookies that need to be
         * invalidated before the second factor authentication.
         *
         * @param string $cookie Cookie string.
         *
         * @return void
         */
        public static function collect_auth_cookie_tokens($cookie)
        {
        }
        /**
         * Get all Two-Factor Auth providers that are enabled for the specified|current user.
         *
         * @param WP_User $user WP_User object of the logged-in user.
         * @return array
         */
        public static function get_enabled_providers_for_user($user = \null)
        {
        }
        /**
         * Get all Two-Factor Auth providers that are both enabled and configured for the specified|current user.
         *
         * @param WP_User $user WP_User object of the logged-in user.
         * @return array
         */
        public static function get_available_providers_for_user($user = \null)
        {
        }
        /**
         * Gets the Two-Factor Auth provider for the specified|current user.
         *
         * @since 0.1-dev
         *
         * @param int $user_id Optional. User ID. Default is 'null'.
         * @return object|null
         */
        public static function get_primary_provider_for_user($user_id = \null)
        {
        }
        /**
         * Quick boolean check for whether a given user is using two-step.
         *
         * @since 0.1-dev
         *
         * @param int $user_id Optional. User ID. Default is 'null'.
         * @return bool
         */
        public static function is_user_using_two_factor($user_id = \null)
        {
        }
        /**
         * Handle the browser-based login.
         *
         * @since 0.1-dev
         *
         * @param string  $user_login Username.
         * @param WP_User $user WP_User object of the logged-in user.
         */
        public static function wp_login($user_login, $user)
        {
        }
        /**
         * Destroy the known password-based authentication sessions for the current user.
         *
         * Is there a better way of finding the current session token without
         * having access to the authentication cookies which are just being set
         * on the first password-based authentication request.
         *
         * @param \WP_User $user User object.
         *
         * @return void
         */
        public static function destroy_current_session_for_user($user)
        {
        }
        /**
         * Prevent login through XML-RPC and REST API for users with at least one
         * two-factor method enabled.
         *
         * @param  WP_User|WP_Error $user Valid WP_User only if the previous filters
         *                                have verified and confirmed the
         *                                authentication credentials.
         *
         * @return WP_User|WP_Error
         */
        public static function filter_authenticate($user)
        {
        }
        /**
         * If the current user can login via API requests such as XML-RPC and REST.
         *
         * @param  integer $user_id User ID.
         *
         * @return boolean
         */
        public static function is_user_api_login_enabled($user_id)
        {
        }
        /**
         * Is the current request an XML-RPC or REST request.
         *
         * @return boolean
         */
        public static function is_api_request()
        {
        }
        /**
         * Display the login form.
         *
         * @since 0.1-dev
         *
         * @param WP_User $user WP_User object of the logged-in user.
         */
        public static function show_two_factor_login($user)
        {
        }
        /**
         * Display the Backup code 2fa screen.
         *
         * @since 0.1-dev
         */
        public static function backup_2fa()
        {
        }
        /**
         * Generates the html form for the second step of the authentication process.
         *
         * @since 0.1-dev
         *
         * @param WP_User       $user WP_User object of the logged-in user.
         * @param string        $login_nonce A string nonce stored in usermeta.
         * @param string        $redirect_to The URL to which the user would like to be redirected.
         * @param string        $error_msg Optional. Login error message.
         * @param string|object $provider An override to the provider.
         */
        public static function login_html($user, $login_nonce, $redirect_to, $error_msg = '', $provider = \null)
        {
        }
        /**
         * Generate the two-factor login form URL.
         *
         * @param  array  $params List of query argument pairs to add to the URL.
         * @param  string $scheme URL scheme context.
         *
         * @return string
         */
        public static function login_url($params = array(), $scheme = 'login')
        {
        }
        /**
         * Create the login nonce.
         *
         * @since 0.1-dev
         *
         * @param int $user_id User ID.
         * @return array
         */
        public static function create_login_nonce($user_id)
        {
        }
        /**
         * Delete the login nonce.
         *
         * @since 0.1-dev
         *
         * @param int $user_id User ID.
         * @return bool
         */
        public static function delete_login_nonce($user_id)
        {
        }
        /**
         * Verify the login nonce.
         *
         * @since 0.1-dev
         *
         * @param int    $user_id User ID.
         * @param string $nonce Login nonce.
         * @return bool
         */
        public static function verify_login_nonce($user_id, $nonce)
        {
        }
        /**
         * Login form validation.
         *
         * @since 0.1-dev
         */
        public static function login_form_validate_2fa()
        {
        }
        /**
         * Filter the columns on the Users admin screen.
         *
         * @param  array $columns Available columns.
         * @return array          Updated array of columns.
         */
        public static function filter_manage_users_columns(array $columns)
        {
        }
        /**
         * Output the 2FA column data on the Users screen.
         *
         * @param  string $output      The column output.
         * @param  string $column_name The column ID.
         * @param  int    $user_id     The user ID.
         * @return string              The column output.
         */
        public static function manage_users_custom_column($output, $column_name, $user_id)
        {
        }
        /**
         * Add user profile fields.
         *
         * This executes during the `show_user_profile` & `edit_user_profile` actions.
         *
         * @since 0.1-dev
         *
         * @param WP_User $user WP_User object of the logged-in user.
         */
        public static function user_two_factor_options($user)
        {
        }
        /**
         * Update the user meta value.
         *
         * This executes during the `personal_options_update` & `edit_user_profile_update` actions.
         *
         * @since 0.1-dev
         *
         * @param int $user_id User ID.
         */
        public static function user_two_factor_options_update($user_id)
        {
        }
        /**
         * Should the login session persist between sessions.
         *
         * @return boolean
         */
        public static function rememberme()
        {
        }
    }
}
namespace u2flib_server {
    class U2F
    {
        /** @var string  */
        private $appId;
        /** @var null|string */
        private $attestDir;
        /** @internal */
        private $FIXCERTS = array('349bca1031f8c82c4ceca38b9cebf1a69df9fb3b94eed99eb3fb9aa3822d26e8', 'dd574527df608e47ae45fbba75a2afdd5c20fd94a02419381813cd55a2a3398f', '1d8764f0f7cd1352df6150045c8f638e517270e8b5dda1c63ade9c2280240cae', 'd0edc9a91a1677435a953390865d208c55b3183c6759c9b5a7ff494c322558eb', '6073c436dcd064a48127ddbf6032ac1a66fd59a0c24434f070d4e564c124c897', 'ca993121846c464d666096d35f13bf44c1b05af205f9b4a1e00cf6cc10c5e511');
        /**
         * @param string $appId Application id for the running application
         * @param string|null $attestDir Directory where trusted attestation roots may be found
         * @throws Error If OpenSSL older than 1.0.0 is used
         */
        public function __construct($appId, $attestDir = null)
        {
        }
        /**
         * Called to get a registration request to send to a user.
         * Returns an array of one registration request and a array of sign requests.
         *
         * @param array $registrations List of current registrations for this
         * user, to prevent the user from registering the same authenticator several
         * times.
         * @return array An array of two elements, the first containing a
         * RegisterRequest the second being an array of SignRequest
         * @throws Error
         */
        public function getRegisterData(array $registrations = array())
        {
        }
        /**
         * Called to verify and unpack a registration message.
         *
         * @param RegisterRequest $request this is a reply to
         * @param object $response response from a user
         * @param bool $includeCert set to true if the attestation certificate should be
         * included in the returned Registration object
         * @return Registration
         * @throws Error
         */
        public function doRegister($request, $response, $includeCert = true)
        {
        }
        /**
         * Called to get an authentication request.
         *
         * @param array $registrations An array of the registrations to create authentication requests for.
         * @return array An array of SignRequest
         * @throws Error
         */
        public function getAuthenticateData(array $registrations)
        {
        }
        /**
         * Called to verify an authentication response
         *
         * @param array $requests An array of outstanding authentication requests
         * @param array $registrations An array of current registrations
         * @param object $response A response from the authenticator
         * @return Registration
         * @throws Error
         *
         * The Registration object returned on success contains an updated counter
         * that should be saved for future authentications.
         * If the Error returned is ERR_COUNTER_TOO_LOW this is an indication of
         * token cloning or similar and appropriate action should be taken.
         */
        public function doAuthenticate(array $requests, array $registrations, $response)
        {
        }
        /**
         * @return array
         */
        private function get_certs()
        {
        }
        /**
         * @param string $data
         * @return string
         */
        private function base64u_encode($data)
        {
        }
        /**
         * @param string $data
         * @return string
         */
        private function base64u_decode($data)
        {
        }
        /**
         * @param string $key
         * @return null|string
         */
        private function pubkey_to_pem($key)
        {
        }
        /**
         * @return string
         * @throws Error
         */
        private function createChallenge()
        {
        }
        /**
         * Fixes a certificate where the signature contains unused bits.
         *
         * @param string $cert
         * @return mixed
         */
        private function fixSignatureUnusedBits($cert)
        {
        }
    }
    /**
     * Class for building a registration request
     *
     * @package u2flib_server
     */
    class RegisterRequest
    {
        /** Protocol version */
        public $version = U2F_VERSION;
        /** Registration challenge */
        public $challenge;
        /** Application id */
        public $appId;
        /**
         * @param string $challenge
         * @param string $appId
         * @internal
         */
        public function __construct($challenge, $appId)
        {
        }
    }
    /**
     * Class for building up an authentication request
     *
     * @package u2flib_server
     */
    class SignRequest
    {
        /** Protocol version */
        public $version = U2F_VERSION;
        /** Authentication challenge */
        public $challenge;
        /** Key handle of a registered authenticator */
        public $keyHandle;
        /** Application id */
        public $appId;
    }
    /**
     * Class returned for successful registrations
     *
     * @package u2flib_server
     */
    class Registration
    {
        /** The key handle of the registered authenticator */
        public $keyHandle;
        /** The public key of the registered authenticator */
        public $publicKey;
        /** The attestation certificate of the registered authenticator */
        public $certificate;
        /** The counter associated with this registration */
        public $counter = -1;
    }
    /**
     * Error class, returned on errors
     *
     * @package u2flib_server
     */
    class Error extends \Exception
    {
        /**
         * Override constructor and make message and code mandatory
         * @param string $message
         * @param int $code
         * @param \Exception|null $previous
         */
        public function __construct($message, $code, \Exception $previous = null)
        {
        }
    }
}
namespace {
    /**
     * Abstract class for creating two factor authentication providers.
     *
     * @package Two_Factor
     */
    /**
     * Abstract class for creating two factor authentication providers.
     *
     * @since 0.1-dev
     *
     * @package Two_Factor
     */
    abstract class Two_Factor_Provider
    {
        /**
         * Class constructor.
         *
         * @since 0.1-dev
         */
        protected function __construct()
        {
        }
        /**
         * Returns the name of the provider.
         *
         * @since 0.1-dev
         *
         * @return string
         */
        public abstract function get_label();
        /**
         * Prints the name of the provider.
         *
         * @since 0.1-dev
         */
        public function print_label()
        {
        }
        /**
         * Prints the form that prompts the user to authenticate.
         *
         * @since 0.1-dev
         *
         * @param WP_User $user WP_User object of the logged-in user.
         */
        public abstract function authentication_page($user);
        /**
         * Allow providers to do extra processing before the authentication.
         * Return `true` to prevent the authentication and render the
         * authentication page.
         *
         * @param  WP_User $user WP_User object of the logged-in user.
         * @return boolean
         */
        public function pre_process_authentication($user)
        {
        }
        /**
         * Validates the users input token.
         *
         * @since 0.1-dev
         *
         * @param WP_User $user WP_User object of the logged-in user.
         * @return boolean
         */
        public abstract function validate_authentication($user);
        /**
         * Whether this Two Factor provider is configured and available for the user specified.
         *
         * @param WP_User $user WP_User object of the logged-in user.
         * @return boolean
         */
        public abstract function is_available_for_user($user);
        /**
         * Generate a random eight-digit string to send out as an auth code.
         *
         * @since 0.1-dev
         *
         * @param int          $length The code length.
         * @param string|array $chars Valid auth code characters.
         * @return string
         */
        public function get_code($length = 8, $chars = '1234567890')
        {
        }
    }
    /**
     * Class for creating a backup codes provider.
     *
     * @package Two_Factor
     */
    /**
     * Class for creating a backup codes provider.
     *
     * @since 0.1-dev
     *
     * @package Two_Factor
     */
    class Two_Factor_Backup_Codes extends \Two_Factor_Provider
    {
        /**
         * The user meta backup codes key.
         *
         * @type string
         */
        const BACKUP_CODES_META_KEY = '_two_factor_backup_codes';
        /**
         * The number backup codes.
         *
         * @type int
         */
        const NUMBER_OF_CODES = 10;
        /**
         * Ensures only one instance of this class exists in memory at any one time.
         *
         * @since 0.1-dev
         */
        public static function get_instance()
        {
        }
        /**
         * Class constructor.
         *
         * @since 0.1-dev
         */
        protected function __construct()
        {
        }
        /**
         * Displays an admin notice when backup codes have run out.
         *
         * @since 0.1-dev
         */
        public function admin_notices()
        {
        }
        /**
         * Returns the name of the provider.
         *
         * @since 0.1-dev
         */
        public function get_label()
        {
        }
        /**
         * Whether this Two Factor provider is configured and codes are available for the user specified.
         *
         * @since 0.1-dev
         *
         * @param WP_User $user WP_User object of the logged-in user.
         * @return boolean
         */
        public function is_available_for_user($user)
        {
        }
        /**
         * Inserts markup at the end of the user profile field for this provider.
         *
         * @since 0.1-dev
         *
         * @param WP_User $user WP_User object of the logged-in user.
         */
        public function user_options($user)
        {
        }
        /**
         * Generates backup codes & updates the user meta.
         *
         * @since 0.1-dev
         *
         * @param WP_User $user WP_User object of the logged-in user.
         * @param array   $args Optional arguments for assigning new codes.
         * @return array
         */
        public function generate_codes($user, $args = '')
        {
        }
        /**
         * Generates a JSON object of backup codes.
         *
         * @since 0.1-dev
         */
        public function ajax_generate_json()
        {
        }
        /**
         * Returns the number of unused codes for the specified user
         *
         * @param WP_User $user WP_User object of the logged-in user.
         * @return int $int  The number of unused codes remaining
         */
        public static function codes_remaining_for_user($user)
        {
        }
        /**
         * Prints the form that prompts the user to authenticate.
         *
         * @since 0.1-dev
         *
         * @param WP_User $user WP_User object of the logged-in user.
         */
        public function authentication_page($user)
        {
        }
        /**
         * Validates the users input token.
         *
         * In this class we just return true.
         *
         * @since 0.1-dev
         *
         * @param WP_User $user WP_User object of the logged-in user.
         * @return boolean
         */
        public function validate_authentication($user)
        {
        }
        /**
         * Validates a backup code.
         *
         * Backup Codes are single use and are deleted upon a successful validation.
         *
         * @since 0.1-dev
         *
         * @param WP_User $user WP_User object of the logged-in user.
         * @param int     $code The backup code.
         * @return boolean
         */
        public function validate_code($user, $code)
        {
        }
        /**
         * Deletes a backup code.
         *
         * @since 0.1-dev
         *
         * @param WP_User $user WP_User object of the logged-in user.
         * @param string  $code_hashed The hashed the backup code.
         */
        public function delete_code($user, $code_hashed)
        {
        }
    }
    /**
     * Class for creating a dummy provider.
     *
     * @package Two_Factor
     */
    /**
     * Class for creating a dummy provider.
     *
     * @since 0.1-dev
     *
     * @package Two_Factor
     */
    class Two_Factor_Dummy extends \Two_Factor_Provider
    {
        /**
         * Ensures only one instance of this class exists in memory at any one time.
         *
         * @since 0.1-dev
         */
        public static function get_instance()
        {
        }
        /**
         * Class constructor.
         *
         * @since 0.1-dev
         */
        protected function __construct()
        {
        }
        /**
         * Returns the name of the provider.
         *
         * @since 0.1-dev
         */
        public function get_label()
        {
        }
        /**
         * Prints the form that prompts the user to authenticate.
         *
         * @since 0.1-dev
         *
         * @param WP_User $user WP_User object of the logged-in user.
         */
        public function authentication_page($user)
        {
        }
        /**
         * Validates the users input token.
         *
         * In this class we just return true.
         *
         * @since 0.1-dev
         *
         * @param WP_User $user WP_User object of the logged-in user.
         * @return boolean
         */
        public function validate_authentication($user)
        {
        }
        /**
         * Whether this Two Factor provider is configured and available for the user specified.
         *
         * @since 0.1-dev
         *
         * @param WP_User $user WP_User object of the logged-in user.
         * @return boolean
         */
        public function is_available_for_user($user)
        {
        }
        /**
         * Inserts markup at the end of the user profile field for this provider.
         *
         * @since 0.1-dev
         *
         * @param WP_User $user WP_User object of the logged-in user.
         */
        public function user_options($user)
        {
        }
    }
    /**
     * Class for creating an email provider.
     *
     * @package Two_Factor
     */
    /**
     * Class for creating an email provider.
     *
     * @since 0.1-dev
     *
     * @package Two_Factor
     */
    class Two_Factor_Email extends \Two_Factor_Provider
    {
        /**
         * The user meta token key.
         *
         * @var string
         */
        const TOKEN_META_KEY = '_two_factor_email_token';
        /**
         * Store the timestamp when the token was generated.
         *
         * @var string
         */
        const TOKEN_META_KEY_TIMESTAMP = '_two_factor_email_token_timestamp';
        /**
         * Name of the input field used for code resend.
         *
         * @var string
         */
        const INPUT_NAME_RESEND_CODE = 'two-factor-email-code-resend';
        /**
         * Ensures only one instance of this class exists in memory at any one time.
         *
         * @since 0.1-dev
         */
        public static function get_instance()
        {
        }
        /**
         * Class constructor.
         *
         * @since 0.1-dev
         */
        protected function __construct()
        {
        }
        /**
         * Returns the name of the provider.
         *
         * @since 0.1-dev
         */
        public function get_label()
        {
        }
        /**
         * Generate the user token.
         *
         * @since 0.1-dev
         *
         * @param int $user_id User ID.
         * @return string
         */
        public function generate_token($user_id)
        {
        }
        /**
         * Check if user has a valid token already.
         *
         * @param  int $user_id User ID.
         * @return boolean      If user has a valid email token.
         */
        public function user_has_token($user_id)
        {
        }
        /**
         * Has the user token validity timestamp expired.
         *
         * @param integer $user_id User ID.
         *
         * @return boolean
         */
        public function user_token_has_expired($user_id)
        {
        }
        /**
         * Get the lifetime of a user token in seconds.
         *
         * @param integer $user_id User ID.
         *
         * @return integer|null Return `null` if the lifetime can't be measured.
         */
        public function user_token_lifetime($user_id)
        {
        }
        /**
         * Return the token time-to-live for a user.
         *
         * @param integer $user_id User ID.
         *
         * @return integer
         */
        public function user_token_ttl($user_id)
        {
        }
        /**
         * Get the authentication token for the user.
         *
         * @param  int $user_id    User ID.
         *
         * @return string|boolean  User token or `false` if no token found.
         */
        public function get_user_token($user_id)
        {
        }
        /**
         * Validate the user token.
         *
         * @since 0.1-dev
         *
         * @param int    $user_id User ID.
         * @param string $token User token.
         * @return boolean
         */
        public function validate_token($user_id, $token)
        {
        }
        /**
         * Delete the user token.
         *
         * @since 0.1-dev
         *
         * @param int $user_id User ID.
         */
        public function delete_token($user_id)
        {
        }
        /**
         * Generate and email the user token.
         *
         * @since 0.1-dev
         *
         * @param WP_User $user WP_User object of the logged-in user.
         * @return bool Whether the email contents were sent successfully.
         */
        public function generate_and_email_token($user)
        {
        }
        /**
         * Prints the form that prompts the user to authenticate.
         *
         * @since 0.1-dev
         *
         * @param WP_User $user WP_User object of the logged-in user.
         */
        public function authentication_page($user)
        {
        }
        /**
         * Send the email code if missing or requested. Stop the authentication
         * validation if a new token has been generated and sent.
         *
         * @param  WP_USer $user WP_User object of the logged-in user.
         * @return boolean
         */
        public function pre_process_authentication($user)
        {
        }
        /**
         * Validates the users input token.
         *
         * @since 0.1-dev
         *
         * @param WP_User $user WP_User object of the logged-in user.
         * @return boolean
         */
        public function validate_authentication($user)
        {
        }
        /**
         * Whether this Two Factor provider is configured and available for the user specified.
         *
         * @since 0.1-dev
         *
         * @param WP_User $user WP_User object of the logged-in user.
         * @return boolean
         */
        public function is_available_for_user($user)
        {
        }
        /**
         * Inserts markup at the end of the user profile field for this provider.
         *
         * @since 0.1-dev
         *
         * @param WP_User $user WP_User object of the logged-in user.
         */
        public function user_options($user)
        {
        }
    }
    /**
     * Class for displaying the list of security key items.
     *
     * @since 0.1-dev
     * @access private
     *
     * @package Two_Factor
     */
    class Two_Factor_FIDO_U2F_Admin_List_Table extends \WP_List_Table
    {
        /**
         * Get a list of columns.
         *
         * @since 0.1-dev
         *
         * @return array
         */
        public function get_columns()
        {
        }
        /**
         * Prepares the list of items for displaying.
         *
         * @since 0.1-dev
         */
        public function prepare_items()
        {
        }
        /**
         * Generates content for a single row of the table
         *
         * @since 0.1-dev
         * @access protected
         *
         * @param object $item The current item.
         * @param string $column_name The current column name.
         * @return string
         */
        protected function column_default($item, $column_name)
        {
        }
        /**
         * Generates custom table navigation to prevent conflicting nonces.
         *
         * @since 0.1-dev
         * @access protected
         *
         * @param string $which The location of the bulk actions: 'top' or 'bottom'.
         */
        protected function display_tablenav($which)
        {
        }
        /**
         * Generates content for a single row of the table
         *
         * @since 0.1-dev
         * @access public
         *
         * @param object $item The current item.
         */
        public function single_row($item)
        {
        }
        /**
         * Outputs the hidden row displayed when inline editing
         *
         * @since 0.1-dev
         */
        public function inline_edit()
        {
        }
    }
    /**
     * Class for registering & modifying FIDO U2F security keys.
     *
     * @package Two_Factor
     */
    /**
     * Class for registering & modifying FIDO U2F security keys.
     *
     * @since 0.1-dev
     *
     * @package Two_Factor
     */
    class Two_Factor_FIDO_U2F_Admin
    {
        /**
         * The user meta register data.
         *
         * @type string
         */
        const REGISTER_DATA_USER_META_KEY = '_two_factor_fido_u2f_register_request';
        /**
         * Add various hooks.
         *
         * @since 0.1-dev
         *
         * @access public
         * @static
         */
        public static function add_hooks()
        {
        }
        /**
         * Enqueue assets.
         *
         * @since 0.1-dev
         *
         * @access public
         * @static
         *
         * @param string $hook Current page.
         */
        public static function enqueue_assets($hook)
        {
        }
        /**
         * Return the current asset version number.
         *
         * Added as own helper to allow swapping the implementation once we inject
         * it as a dependency.
         *
         * @return string
         */
        protected static function asset_version()
        {
        }
        /**
         * Display the security key section in a users profile.
         *
         * This executes during the `show_user_security_settings` action.
         *
         * @since 0.1-dev
         *
         * @access public
         * @static
         *
         * @param WP_User $user WP_User object of the logged-in user.
         */
        public static function show_user_profile($user)
        {
        }
        /**
         * Catch the non-ajax submission from the new form.
         *
         * This executes during the `personal_options_update` & `edit_user_profile_update` actions.
         *
         * @since 0.1-dev
         *
         * @access public
         * @static
         *
         * @param int $user_id User ID.
         * @return false
         */
        public static function catch_submission($user_id)
        {
        }
        /**
         * Catch the delete security key request.
         *
         * This executes during the `load-profile.php` & `load-user-edit.php` actions.
         *
         * @since 0.1-dev
         *
         * @access public
         * @static
         */
        public static function catch_delete_security_key()
        {
        }
        /**
         * Generate a link to rename a specified security key.
         *
         * @since 0.1-dev
         *
         * @access public
         * @static
         *
         * @param array $item The current item.
         * @return string
         */
        public static function rename_link($item)
        {
        }
        /**
         * Generate a link to delete a specified security key.
         *
         * @since 0.1-dev
         *
         * @access public
         * @static
         *
         * @param array $item The current item.
         * @return string
         */
        public static function delete_link($item)
        {
        }
        /**
         * Ajax handler for quick edit saving for a security key.
         *
         * @since 0.1-dev
         *
         * @access public
         * @static
         */
        public static function wp_ajax_inline_save()
        {
        }
    }
    /**
     * Class for creating a FIDO Universal 2nd Factor provider.
     *
     * @package Two_Factor
     */
    /**
     * Class for creating a FIDO Universal 2nd Factor provider.
     *
     * @since 0.1-dev
     *
     * @package Two_Factor
     */
    class Two_Factor_FIDO_U2F extends \Two_Factor_Provider
    {
        /**
         * U2F Library
         *
         * @var u2flib_server\U2F
         */
        public static $u2f;
        /**
         * The user meta registered key.
         *
         * @type string
         */
        const REGISTERED_KEY_USER_META_KEY = '_two_factor_fido_u2f_registered_key';
        /**
         * The user meta authenticate data.
         *
         * @type string
         */
        const AUTH_DATA_USER_META_KEY = '_two_factor_fido_u2f_login_request';
        /**
         * Version number for the bundled assets.
         *
         * @var string
         */
        const U2F_ASSET_VERSION = '0.2.1';
        /**
         * Ensures only one instance of this class exists in memory at any one time.
         *
         * @return \Two_Factor_FIDO_U2F
         */
        public static function get_instance()
        {
        }
        /**
         * Class constructor.
         *
         * @since 0.1-dev
         */
        protected function __construct()
        {
        }
        /**
         * Get the asset version number.
         *
         * TODO: There should be a plugin-level helper for getting the current plugin version.
         *
         * @return string
         */
        public static function asset_version()
        {
        }
        /**
         * Return the U2F AppId. U2F requires the AppID to use HTTPS
         * and a top-level domain.
         *
         * @return string AppID URI
         */
        public static function get_u2f_app_id()
        {
        }
        /**
         * Returns the name of the provider.
         *
         * @since 0.1-dev
         */
        public function get_label()
        {
        }
        /**
         * Register script dependencies used during login and when
         * registering keys in the WP admin.
         *
         * @return void
         */
        public static function enqueue_scripts()
        {
        }
        /**
         * Prints the form that prompts the user to authenticate.
         *
         * @since 0.1-dev
         *
         * @param WP_User $user WP_User object of the logged-in user.
         * @return null
         */
        public function authentication_page($user)
        {
        }
        /**
         * Validates the users input token.
         *
         * @since 0.1-dev
         *
         * @param WP_User $user WP_User object of the logged-in user.
         * @return boolean
         */
        public function validate_authentication($user)
        {
        }
        /**
         * Whether this Two Factor provider is configured and available for the user specified.
         *
         * @since 0.1-dev
         *
         * @param WP_User $user WP_User object of the logged-in user.
         * @return boolean
         */
        public function is_available_for_user($user)
        {
        }
        /**
         * Inserts markup at the end of the user profile field for this provider.
         *
         * @since 0.1-dev
         *
         * @param WP_User $user WP_User object of the logged-in user.
         */
        public function user_options($user)
        {
        }
        /**
         * Add registered security key to a user.
         *
         * @since 0.1-dev
         *
         * @param int    $user_id  User ID.
         * @param object $register The data of registered security key.
         * @return int|bool Meta ID on success, false on failure.
         */
        public static function add_security_key($user_id, $register)
        {
        }
        /**
         * Retrieve registered security keys for a user.
         *
         * @since 0.1-dev
         *
         * @param int $user_id User ID.
         * @return array|bool Array of keys on success, false on failure.
         */
        public static function get_security_keys($user_id)
        {
        }
        /**
         * Update registered security key.
         *
         * Use the $prev_value parameter to differentiate between meta fields with the
         * same key and user ID.
         *
         * If the meta field for the user does not exist, it will be added.
         *
         * @since 0.1-dev
         *
         * @param int    $user_id  User ID.
         * @param object $data The data of registered security key.
         * @return int|bool Meta ID if the key didn't exist, true on successful update, false on failure.
         */
        public static function update_security_key($user_id, $data)
        {
        }
        /**
         * Remove registered security key matching criteria from a user.
         *
         * @since 0.1-dev
         *
         * @param int    $user_id   User ID.
         * @param string $keyHandle Optional. Key handle.
         * @return bool True on success, false on failure.
         */
        public static function delete_security_key($user_id, $keyHandle = \null)
        {
        }
    }
    /**
     * Class for creating a Time Based One-Time Password provider.
     *
     * @package Two_Factor
     */
    /**
     * Class Two_Factor_Totp
     */
    class Two_Factor_Totp extends \Two_Factor_Provider
    {
        /**
         * The user meta token key.
         *
         * @var string
         */
        const SECRET_META_KEY = '_two_factor_totp_key';
        /**
         * The user meta token key.
         *
         * @var string
         */
        const NOTICES_META_KEY = '_two_factor_totp_notices';
        /**
         * Action name for resetting the secret token.
         *
         * @var string
         */
        const ACTION_SECRET_DELETE = 'totp-delete';
        const DEFAULT_KEY_BIT_SIZE = 160;
        const DEFAULT_CRYPTO = 'sha1';
        const DEFAULT_DIGIT_COUNT = 6;
        const DEFAULT_TIME_STEP_SEC = 30;
        const DEFAULT_TIME_STEP_ALLOWANCE = 4;
        /**
         * Chracters used in base32 encoding.
         *
         * @var string
         */
        private static $base_32_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        /**
         * Class constructor. Sets up hooks, etc.
         */
        protected function __construct()
        {
        }
        /**
         * Ensures only one instance of this class exists in memory at any one time.
         */
        public static function get_instance()
        {
        }
        /**
         * Returns the name of the provider.
         */
        public function get_label()
        {
        }
        /**
         * Trigger our custom user settings actions.
         *
         * @param integer $user_id User ID.
         * @param string  $action Action ID.
         *
         * @return void
         */
        public function user_settings_action($user_id, $action)
        {
        }
        /**
         * Get the URL for deleting the secret token.
         *
         * @param integer $user_id User ID.
         *
         * @return string
         */
        protected function get_token_delete_url_for_user($user_id)
        {
        }
        /**
         * Display TOTP options on the user settings page.
         *
         * @param WP_User $user The current user being edited.
         * @return false
         */
        public function user_two_factor_options($user)
        {
        }
        /**
         * Save the options specified in `::user_two_factor_options()`
         *
         * @param integer $user_id The user ID whose options are being updated.
         *
         * @return void
         */
        public function user_two_factor_options_update($user_id)
        {
        }
        /**
         * Get the TOTP secret key for a user.
         *
         * @param  int $user_id User ID.
         *
         * @return string
         */
        public function get_user_totp_key($user_id)
        {
        }
        /**
         * Set the TOTP secret key for a user.
         *
         * @param int    $user_id User ID.
         * @param string $key TOTP secret key.
         *
         * @return boolean If the key was stored successfully.
         */
        public function set_user_totp_key($user_id, $key)
        {
        }
        /**
         * Delete the TOTP secret key for a user.
         *
         * @param  int $user_id User ID.
         *
         * @return boolean If the key was deleted successfully.
         */
        public function delete_user_totp_key($user_id)
        {
        }
        /**
         * Check if the TOTP secret key has a proper format.
         *
         * @param  string $key TOTP secret key.
         *
         * @return boolean
         */
        public function is_valid_key($key)
        {
        }
        /**
         * Display any available admin notices.
         *
         * @param integer $user_id User ID.
         *
         * @return void
         */
        public function admin_notices($user_id)
        {
        }
        /**
         * Validates authentication.
         *
         * @param WP_User $user WP_User object of the logged-in user.
         *
         * @return bool Whether the user gave a valid code
         */
        public function validate_authentication($user)
        {
        }
        /**
         * Checks if a given code is valid for a given key, allowing for a certain amount of time drift
         *
         * @param string $key      The share secret key to use.
         * @param string $authcode The code to test.
         *
         * @return bool Whether the code is valid within the time frame
         */
        public static function is_valid_authcode($key, $authcode)
        {
        }
        /**
         * Generates key
         *
         * @param int $bitsize Nume of bits to use for key.
         *
         * @return string $bitsize long string composed of available base32 chars.
         */
        public static function generate_key($bitsize = self::DEFAULT_KEY_BIT_SIZE)
        {
        }
        /**
         * Pack stuff
         *
         * @param string $value The value to be packed.
         *
         * @return string Binary packed string.
         */
        public static function pack64($value)
        {
        }
        /**
         * Calculate a valid code given the shared secret key
         *
         * @param string $key        The shared secret key to use for calculating code.
         * @param mixed  $step_count The time step used to calculate the code, which is the floor of time() divided by step size.
         * @param int    $digits     The number of digits in the returned code.
         * @param string $hash       The hash used to calculate the code.
         * @param int    $time_step  The size of the time step.
         *
         * @return string The totp code
         */
        public static function calc_totp($key, $step_count = \false, $digits = self::DEFAULT_DIGIT_COUNT, $hash = self::DEFAULT_CRYPTO, $time_step = self::DEFAULT_TIME_STEP_SEC)
        {
        }
        /**
         * Uses the Google Charts API to build a QR Code for use with an otpauth url
         *
         * @param string $name  The name to display in the Authentication app.
         * @param string $key   The secret key to share with the Authentication app.
         * @param string $title The title to display in the Authentication app.
         *
         * @return string A URL to use as an img src to display the QR code
         */
        public static function get_google_qr_code($name, $key, $title = \null)
        {
        }
        /**
         * Whether this Two Factor provider is configured and available for the user specified.
         *
         * @param WP_User $user WP_User object of the logged-in user.
         *
         * @return boolean
         */
        public function is_available_for_user($user)
        {
        }
        /**
         * Prints the form that prompts the user to authenticate.
         *
         * @param WP_User $user WP_User object of the logged-in user.
         */
        public function authentication_page($user)
        {
        }
        /**
         * Returns a base32 encoded string.
         *
         * @param string $string String to be encoded using base32.
         *
         * @return string base32 encoded string without padding.
         */
        public static function base32_encode($string)
        {
        }
        /**
         * Decode a base32 string and return a binary representation
         *
         * @param string $base32_string The base 32 string to decode.
         *
         * @throws Exception If string contains non-base32 characters.
         *
         * @return string Binary representation of decoded string
         */
        public static function base32_decode($base32_string)
        {
        }
        /**
         * Used with usort to sort an array by distance from 0
         *
         * @param int $a First array element.
         * @param int $b Second array element.
         *
         * @return int -1, 0, or 1 as needed by usort
         */
        private static function abssort($a, $b)
        {
        }
    }
}
namespace {
    /**
     * Extracted from wp-login.php since that file also loads WP core which we already have.
     */
    /**
     * Outputs the footer for the login page.
     *
     * @since 3.1.0
     *
     * @global bool|string $interim_login Whether interim login modal is being displayed. String 'success'
     *                                    upon successful login.
     *
     * @param string $input_id Which input to auto-focus.
     */
    function login_footer($input_id = '')
    {
    }
    /**
     * Outputs the JavaScript to handle the form shaking on the login page.
     *
     * @since 3.0.0
     */
    function wp_shake_js()
    {
    }
    /**
     * Extracted from wp-login.php since that file also loads WP core which we already have.
     */
    /**
     * Output the login page header.
     *
     * @since 2.1.0
     *
     * @global string      $error         Login error message set by deprecated pluggable wp_login() function
     *                                    or plugins replacing it.
     * @global bool|string $interim_login Whether interim login modal is being displayed. String 'success'
     *                                    upon successful login.
     * @global string      $action        The action that brought the visitor to the login page.
     *
     * @param string   $title    Optional. WordPress login Page title to display in the `<title>` element.
     *                           Default 'Log In'.
     * @param string   $message  Optional. Message to display in header. Default empty.
     * @param WP_Error $wp_error Optional. The error to pass. Default is a WP_Error instance.
     */
    function login_header($title = 'Log In', $message = '', $wp_error = \null)
    {
    }
    // End of login_header().
    /**
     * Outputs the viewport meta tag for the login page.
     *
     * @since 3.7.0
     */
    function wp_login_viewport_meta()
    {
    }
}