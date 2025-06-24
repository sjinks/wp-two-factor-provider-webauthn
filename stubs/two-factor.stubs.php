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
         * Ensures only one instance of the provider class exists in memory at any one time.
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
         *
         * @return string
         */
        abstract public function get_label();
        /**
         * Returns the "continue with" text provider for the login screen.
         *
         * @since 0.9.0
         *
         * @return string
         */
        public function get_alternative_provider_label()
        {
        }
        /**
         * Prints the name of the provider.
         *
         * @since 0.1-dev
         */
        public function print_label()
        {
        }
        /**
         * Retrieves the provider key / slug.
         *
         * @since 0.9.0
         *
         * @return string
         */
        public function get_key()
        {
        }
        /**
         * Prints the form that prompts the user to authenticate.
         *
         * @since 0.1-dev
         *
         * @param WP_User $user WP_User object of the logged-in user.
         */
        abstract public function authentication_page($user);
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
        abstract public function validate_authentication($user);
        /**
         * Whether this Two Factor provider is configured and available for the user specified.
         *
         * @param WP_User $user WP_User object of the logged-in user.
         * @return boolean
         */
        abstract public function is_available_for_user($user);
        /**
         * If this provider should be available for the user.
         *
         * @param WP_User|int $user WP_User object, user ID or null to resolve the current user.
         *
         * @return bool
         */
        public static function is_supported_for_user($user = \null)
        {
        }
        /**
         * Generate a random eight-digit string to send out as an auth code.
         *
         * @since 0.1-dev
         *
         * @param int          $length The code length.
         * @param string|array $chars Valid auth code characters.
         * @return string
         */
        public static function get_code($length = 8, $chars = '1234567890')
        {
        }
        /**
         * Sanitizes a numeric code to be used as an auth code.
         *
         * @param string $field  The _REQUEST field to check for the code.
         * @param int    $length The valid expected length of the field.
         * @return false|string Auth code on success, false if the field is not set or not expected length.
         */
        public static function sanitize_code_from_request($field, $length = 0)
        {
        }
        /**
         * Return the user meta keys that need to be deletated on plugin uninstall.
         *
         * @return array
         */
        public static function uninstall_user_meta_keys()
        {
        }
        /**
         * Return the option keys that need to be deleted on plugin uninstall.
         *
         * Note: this method doesn't have access to the instantiated provider object.
         *
         * @return array
         */
        public static function uninstall_options()
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
         * Returns the "continue with" text provider for the login screen.
         *
         * @since 0.9.0
         */
        public function get_alternative_provider_label()
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
         * Return user meta keys to delete during plugin uninstall.
         *
         * @return array
         */
        public static function uninstall_user_meta_keys()
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
         * Returns the "continue with" text provider for the login screen.
         *
         * @since 0.9.0
         */
        public function get_alternative_provider_label()
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
         * @return void
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
        /**
         * Return user meta keys to delete during plugin uninstall.
         *
         * @return array
         */
        public static function uninstall_user_meta_keys()
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
         * Class constructor.
         *
         * @since 0.1-dev
         *
         * @codeCoverageIgnore
         */
        protected function __construct()
        {
        }
        /**
         * Register the rest-api endpoints required for this provider.
         *
         * @codeCoverageIgnore
         */
        public function register_rest_routes()
        {
        }
        /**
         * Displays an admin notice when backup codes have run out.
         *
         * @since 0.1-dev
         *
         * @codeCoverageIgnore
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
         * Returns the "continue with" text provider for the login screen.
         *
         * @since 0.9.0
         */
        public function get_alternative_provider_label()
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
         * Generates Backup Codes for returning through the WordPress Rest API.
         *
         * @since 0.8.0
         */
        public function rest_generate_codes($request)
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
        /**
         * Return user meta keys to delete during plugin uninstall.
         *
         * @return array
         */
        public static function uninstall_user_meta_keys()
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
         * @return void|never
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
         * The user meta key for the TOTP Secret key.
         *
         * @var string
         */
        const SECRET_META_KEY = '_two_factor_totp_key';
        /**
         * The user meta key for the last successful TOTP token timestamp logged in with.
         *
         * @var string
         */
        const LAST_SUCCESSFUL_LOGIN_META_KEY = '_two_factor_totp_last_successful_login';
        const DEFAULT_KEY_BIT_SIZE = 160;
        const DEFAULT_CRYPTO = 'sha1';
        const DEFAULT_DIGIT_COUNT = 6;
        const DEFAULT_TIME_STEP_SEC = 30;
        const DEFAULT_TIME_STEP_ALLOWANCE = 4;
        /**
         * Class constructor. Sets up hooks, etc.
         *
         * @codeCoverageIgnore
         */
        protected function __construct()
        {
        }
        /**
         * Register the rest-api endpoints required for this provider.
         *
         * @codeCoverageIgnore
         */
        public function register_rest_routes()
        {
        }
        /**
         * Returns the name of the provider.
         */
        public function get_label()
        {
        }
        /**
         * Returns the "continue with" text provider for the login screen.
         *
         * @since 0.9.0
         */
        public function get_alternative_provider_label()
        {
        }
        /**
         * Enqueue scripts
         *
         * @codeCoverageIgnore
         */
        public function enqueue_assets($hook_suffix)
        {
        }
        /**
         * Rest API endpoint for handling deactivation of TOTP.
         *
         * @param WP_REST_Request $request The Rest Request object.
         * @return array Success array.
         */
        public function rest_delete_totp($request)
        {
        }
        /**
         * REST API endpoint for setting up TOTP.
         *
         * @param WP_REST_Request $request The Rest Request object.
         * @return WP_Error|array Array of data on success, WP_Error on error.
         */
        public function rest_setup_totp($request)
        {
        }
        /**
         * Generates a URL that can be used to create a QR code.
         *
         * @param WP_User $user       The user to generate a URL for.
         * @param string  $secret_key The secret key.
         *
         * @return string
         */
        public static function generate_qr_code_url($user, $secret_key)
        {
        }
        /**
         * Display TOTP options on the user settings page.
         *
         * @param WP_User $user The current user being edited.
         * @return void
         *
         * @codeCoverageIgnore
         */
        public function user_two_factor_options($user)
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
         * Validates an authentication code for a given user, preventing re-use and older TOTP keys.
         *
         * @param WP_User $user WP_User object of the logged-in user.
         * @param int     $code The TOTP token to validate.
         *
         * @return bool Whether the code is valid for the user and a newer code has not been used.
         */
        public function validate_code_for_user($user, $code)
        {
        }
        /**
         * Checks if a given code is valid for a given key, allowing for a certain amount of time drift.
         *
         * @param string $key      The share secret key to use.
         * @param string $authcode The code to test.
         *
         * @return bool Whether the code is valid within the time frame.
         */
        public static function is_valid_authcode($key, $authcode)
        {
        }
        /**
         * Checks if a given code is valid for a given key, allowing for a certain amount of time drift.
         *
         * @param string $key      The share secret key to use.
         * @param string $authcode The code to test.
         *
         * @return false|int Returns the timestamp of the authcode on success, False otherwise.
         */
        public static function get_authcode_valid_ticktime($key, $authcode)
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
         *
         * @codeCoverageIgnore
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
         * Return user meta keys to delete during plugin uninstall.
         *
         * @return array
         */
        public static function uninstall_user_meta_keys()
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
         * The user meta key to store the last failed timestamp.
         *
         * @type string
         */
        const USER_RATE_LIMIT_KEY = '_two_factor_last_login_failure';
        /**
         * The user meta key to store the number of failed login attempts.
         *
         * @var string
         */
        const USER_FAILED_LOGIN_ATTEMPTS_KEY = '_two_factor_failed_login_attempts';
        /**
         * The user meta key to store whether or not the password was reset.
         *
         * @var string
         */
        const USER_PASSWORD_WAS_RESET_KEY = '_two_factor_password_was_reset';
        /**
         * URL query parameter used for our custom actions.
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
         * Namespace for plugin rest api endpoints.
         *
         * @var string
         */
        const REST_NAMESPACE = 'two-factor/1.0';
        /**
         * Set up filters and actions.
         *
         * @param object $compat A compatibility layer for plugins.
         *
         * @since 0.1-dev
         */
        public static function add_hooks($compat)
        {
        }
        /**
         * Delete all plugin data on uninstall.
         *
         * @return void
         */
        public static function uninstall()
        {
        }
        /**
         * Get all registered two-factor providers with keys as the original
         * provider class names and the values as the provider class instances.
         *
         * @see Two_Factor_Core::get_enabled_providers_for_user()
         * @see Two_Factor_Core::get_supported_providers_for_user()
         *
         * @since 0.1-dev
         *
         * @return array
         */
        public static function get_providers()
        {
        }
        /**
         * Get providers available for user which may not be enabled or configured.
         *
         * @see Two_Factor_Core::get_enabled_providers_for_user()
         * @see Two_Factor_Core::get_available_providers_for_user()
         *
         * @param  WP_User|int|null $user User ID.
         * @return array List of provider instances indexed by provider key.
         */
        public static function get_supported_providers_for_user($user = \null)
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
         * Get the two-factor revalidate URL.
         *
         * @param bool $interim If the URL should load the interim login iframe modal.
         * @return string
         */
        public static function get_user_two_factor_revalidate_url($interim = \false)
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
         * Fetch the WP_User object for a provided input.
         *
         * @since 0.8.0
         *
         * @param int|WP_User $user Optional. The WP_User or user ID. Defaults to current user.
         *
         * @return false|WP_User WP_User on success, false on failure.
         */
        public static function fetch_user($user = \null)
        {
        }
        /**
         * Get two-factor providers that are enabled for the specified (or current) user
         * but might not be configured, yet.
         *
         * @see Two_Factor_Core::get_supported_providers_for_user()
         * @see Two_Factor_Core::get_available_providers_for_user()
         *
         * @param int|WP_User $user Optional. User ID, or WP_User object of the the user. Defaults to current user.
         * @return array
         */
        public static function get_enabled_providers_for_user($user = \null)
        {
        }
        /**
         * Get all two-factor providers that are both enabled and configured
         * for the specified (or current) user.
         *
         * @see Two_Factor_Core::get_supported_providers_for_user()
         * @see Two_Factor_Core::get_enabled_providers_for_user()
         *
         * @param int|WP_User $user Optional. User ID, or WP_User object of the the user. Defaults to current user.
         * @return array List of provider instances.
         */
        public static function get_available_providers_for_user($user = \null)
        {
        }
        /**
         * Fetch the provider for the request based on the user preferences.
         *
         * @param int|WP_User        $user Optional. User ID, or WP_User object of the the user. Defaults to current user.
         * @param null|string|object $preferred_provider Optional. The name of the provider, the provider, or empty.
         * @return null|object The provider
         */
        public static function get_provider_for_user($user = \null, $preferred_provider = \null)
        {
        }
        /**
         * Gets the Two-Factor Auth provider for the specified|current user.
         *
         * @since 0.1-dev
         *
         * @param int|WP_User $user Optional. User ID, or WP_User object of the the user. Defaults to current user.
         * @return object|null
         */
        public static function get_primary_provider_for_user($user = \null)
        {
        }
        /**
         * Quick boolean check for whether a given user is using two-step.
         *
         * @since 0.1-dev
         *
         * @param int|WP_User $user Optional. User ID, or WP_User object of the the user. Defaults to current user.
         * @return bool
         */
        public static function is_user_using_two_factor($user = \null)
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
         * Prevent login cookies being set on login for Two Factor users.
         *
         * This makes it so that Core never sends the auth cookies. `login_form_validate_2fa()` will send them manually once the 2nd factor has been verified.
         *
         * @param  WP_User|WP_Error $user Valid WP_User only if the previous filters
         *                                have verified and confirmed the
         *                                authentication credentials.
         *
         * @return WP_User|WP_Error
         */
        public static function filter_authenticate_block_cookies($user)
        {
        }
        /**
         * If the current user can login via API requests such as XML-RPC and REST.
         *
         * @param integer $user_id User ID.
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
         * Displays a message informing the user that their account has had failed login attempts.
         *
         * @param WP_User $user WP_User object of the logged-in user.
         */
        public static function maybe_show_last_login_failure_notice($user)
        {
        }
        /**
         * Show the password reset notice if the user's password was reset.
         *
         * They were also sent an email notification in `send_password_reset_email()`, but email sent from a typical
         * web server is not reliable enough to trust completely.
         *
         * @param WP_Error $errors
         */
        public static function maybe_show_reset_password_notice($errors)
        {
        }
        /**
         * Clear the password reset notice after the user resets their password.
         *
         * @param WP_User $user
         */
        public static function clear_password_reset_notice($user)
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
        public static function login_html($user, $login_nonce, $redirect_to, $error_msg = '', $provider = \null, $action = 'validate_2fa')
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
         * Get the hash of a nonce for storage and comparison.
         *
         * @param array $nonce Nonce array to be hashed. ⚠️ This must contain user ID and expiration,
         *                     to guarantee the nonce only works for the intended user during the
         *                     intended time window.
         *
         * @return string|false
         */
        protected static function hash_login_nonce($nonce)
        {
        }
        /**
         * Create the login nonce.
         *
         * @since 0.1-dev
         *
         * @param int $user_id User ID.
         * @return array|false
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
         * Determine the minimum wait between two factor attempts for a user.
         *
         * This implements an increasing backoff, requiring an attacker to wait longer
         * each time to attempt to brute-force the login.
         *
         * @param WP_User $user The user being operated upon.
         * @return int Time delay in seconds between login attempts.
         */
        public static function get_user_time_delay($user)
        {
        }
        /**
         * Determine if a time delay between user two factor login attempts should be triggered.
         *
         * @since 0.8.0
         *
         * @param WP_User $user The User.
         * @return bool True if rate limit is okay, false if not.
         */
        public static function is_user_rate_limited($user)
        {
        }
        /**
         * Determine if the current user session is logged in with 2FA.
         *
         * @since 0.9.0
         *
         * @return int|false The last time the two-factor was validated on success, false if not currently using a 2FA session.
         */
        public static function is_current_user_session_two_factor()
        {
        }
        /**
         * Determine if the current user session can update Two-Factor settings.
         *
         * @param string $context The context in use, 'display' or 'save'. Save has twice the grace time.
         *
         * @return bool
         */
        public static function current_user_can_update_two_factor_options($context = 'display')
        {
        }
        /**
         * Validate that the current user can edit the specified user. If two-factor is required by the account, also verify that it's within the revalidation grace period.
         *
         * @param int $user_id The user ID being updated.
         *
         * @return bool|\WP_Error
         */
        public static function rest_api_can_edit_user_and_update_two_factor_options($user_id)
        {
        }
        /**
         * Login form validation handler.
         *
         * @since 0.1-dev
         */
        public static function login_form_validate_2fa()
        {
        }
        /**
         * Login form validation.
         *
         * This function exists for unit testing, as `exit` prevents testing.
         * This function expects the caller exiting after calling.
         *
         * @since 0.9.0
         *
         * @param WP_User $user            The WP_User instance.
         * @param string  $nonce           The nonce provided.
         * @param string  $provider        The provider to use, if known.
         * @param string  $redirect_to     The redirection location.
         * @param bool    $is_post_request Whether the incoming request was a POST request or not.
         * @return void
         */
        public static function _login_form_validate_2fa($user, $nonce = '', $provider = '', $redirect_to = '', $is_post_request = \false)
        {
        }
        /**
         * Display the "Revalidate Two Factor" page.
         *
         * @since 0.9.0
         */
        public static function login_form_revalidate_2fa()
        {
        }
        /**
         * Revalidate form validation.
         *
         * This function exists for unit testing, as `exit` prevents testing.
         * This function expects the caller exiting after calling.
         *
         * @since 0.9.0
         *
         * @param string  $nonce           The nonce passed with the request.
         * @param string  $provider        The provider to use, if known.
         * @param string  $redirect_to     The redirection location.
         * @param bool    $is_post_request Whether the incoming request was a POST request or not.
         * @return void
         */
        public static function _login_form_revalidate_2fa($nonce = '', $provider = '', $redirect_to = '', $is_post_request = \false)
        {
        }
        /**
         * Process the 2FA provider authentication.
         *
         * @param object  $provider        The Two Factor Provider.
         * @param WP_User $user            The user being authenticated.
         * @param bool    $is_post_request Whether the request is a POST request.
         * @return false|WP_Error|true WP_Error when an error occurs, true when the user is authenticated, false if no action occurred.
         */
        public static function process_provider($provider, $user, $is_post_request)
        {
        }
        /**
         * Determine if the user's password should be reset.
         *
         * @param int $user_id
         *
         * @return bool
         */
        public static function should_reset_password($user_id)
        {
        }
        /**
         * Reset a compromised password.
         *
         * If we know that the the password is compromised, we have the responsibility to reset it and inform the
         * user. `get_user_time_delay()` mitigates brute force attempts, but this acts as an extra layer of defense
         * which guarantees that attackers can't brute force it (unless they compromise the new password).
         *
         * @param WP_User $user The user who failed to login
         */
        public static function reset_compromised_password($user)
        {
        }
        /**
         * Notify the user and admin that a password was reset for being compromised.
         *
         * @param WP_User $user The user whose password should be reset
         */
        public static function send_password_reset_emails($user)
        {
        }
        /**
         * Notify the user that their password has been compromised and reset.
         *
         * @param WP_User $user The user to notify
         *
         * @return bool `true` if the email was sent, `false` if it failed.
         */
        public static function notify_user_password_reset($user)
        {
        }
        /**
         * Notify the admin that a user's password was compromised and reset.
         *
         * @param WP_User $user The user whose password was reset.
         *
         * @return bool `true` if the email was sent, `false` if it failed.
         */
        public static function notify_admin_user_password_reset($user)
        {
        }
        /**
         * Show the password reset error when on the login screen.
         */
        public static function show_password_reset_error()
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
         * Enable a provider for a user.
         *
         * The caller is responsible for checking the user has permission to do this.
         *
         * @param int    $user_id      The ID of the user.
         * @param string $new_provider The name of the provider class.
         *
         * @return bool True if the provider was enabled, false otherwise.
         */
        public static function enable_provider_for_user($user_id, $new_provider)
        {
        }
        /**
         * Disable a provider for a user.
         *
         * This intentionally doesn't set a new primary provider when disabling the current primary provider, because
         * `get_primary_provider_for_user()` will pick a new one automatically.
         *
         * The caller is responsible for checking the user has permission to do this.
         *
         * @param int    $user_id  The ID of the user.
         * @param string $provider The name of the provider class.
         *
         * @return bool True if the provider was disabled, false otherwise.
         */
        public static function disable_provider_for_user($user_id, $provider_to_delete)
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
         * Update the current user session metadata.
         *
         * Any values set in $data that are null will be removed from the user session metadata.
         *
         * @param array $data The data to append/remove from the current session.
         * @return bool
         */
        public static function update_current_user_session($data = array())
        {
        }
        /**
         * Fetch the current user session metadata.
         *
         * @return false|array The session array, false on error.
         */
        public static function get_current_user_session()
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
        /**
         * Sync the Two-Factor session data from the current session to newly created sessions.
         *
         * This is required as WordPress creates a new session when the user password is changed.
         *
         * @see https://core.trac.wordpress.org/ticket/58427
         *
         * @param array $session The Session information.
         * @param int   $user_id The User ID for the session.
         * @return array
         */
        public static function filter_session_information($session, $user_id)
        {
        }
    }
}
namespace u2flib_server {
    class U2F
    {
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
        public function __construct($message, $code, ?\Exception $previous = null)
        {
        }
    }
}
/* Copyright (c) 2014 Yubico AB
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are
 * met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above
 *     copyright notice, this list of conditions and the following
 *     disclaimer in the documentation and/or other materials provided
 *     with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
namespace u2flib_server {
    /** Constant for the version of the u2f protocol */
    const U2F_VERSION = "U2F_V2";
    /** Error for the authentication message not matching any outstanding
     * authentication request */
    const ERR_NO_MATCHING_REQUEST = 1;
    /** Error for the authentication message not matching any registration */
    const ERR_NO_MATCHING_REGISTRATION = 2;
    /** Error for the signature on the authentication message not verifying with
     * the correct key */
    const ERR_AUTHENTICATION_FAILURE = 3;
    /** Error for the challenge in the registration message not matching the
     * registration challenge */
    const ERR_UNMATCHED_CHALLENGE = 4;
    /** Error for the attestation signature on the registration message not
     * verifying */
    const ERR_ATTESTATION_SIGNATURE = 5;
    /** Error for the attestation verification not verifying */
    const ERR_ATTESTATION_VERIFICATION = 6;
    /** Error for not getting good random from the system */
    const ERR_BAD_RANDOM = 7;
    /** Error when the counter is lower than expected */
    const ERR_COUNTER_TOO_LOW = 8;
    /** Error decoding public key */
    const ERR_PUBKEY_DECODE = 9;
    /** Error user-agent returned error */
    const ERR_BAD_UA_RETURNING = 10;
    /** Error old OpenSSL version */
    const ERR_OLD_OPENSSL = 11;
    /** @internal */
    const PUBKEY_LEN = 65;
}
namespace {
    /**
     * Two Factor
     *
     * @package     Two_Factor
     * @author      WordPress.org Contributors
     * @copyright   2020 Plugin Contributors
     * @license     GPL-2.0-or-later
     *
     * @wordpress-plugin
     * Plugin Name:       Two Factor
     * Plugin URI:        https://wordpress.org/plugins/two-factor/
     * Description:       Enable Two-Factor Authentication using time-based one-time passwords, Universal 2nd Factor (FIDO U2F, YubiKey), email, and backup verification codes.
     * Version:           0.13.0
     * Requires at least: 6.3
     * Requires PHP:      7.2
     * Author:            WordPress.org Contributors
     * Author URI:        https://github.com/wordpress/two-factor/graphs/contributors
     * License:           GPL-2.0-or-later
     * License URI:       https://spdx.org/licenses/GPL-2.0-or-later.html
     * Text Domain:       two-factor
     * Network:           True
     */
    /**
     * Shortcut constant to the path of this file.
     */
    \define('TWO_FACTOR_DIR', \plugin_dir_path(__FILE__));
    /**
     * Version of the plugin.
     */
    \define('TWO_FACTOR_VERSION', '0.13.0');
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
}
