<?php

use WildWolf\WordPress\TwoFactorWebAuthn\WebAuthn_Provider;

/**
 * @psalm-api
 * @psalm-method static bool is_supported_for_user( WP_User|int|null $user = null )
 */
class TwoFactor_Provider_WebAuthn extends WebAuthn_Provider {

}
