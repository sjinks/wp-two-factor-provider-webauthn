<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn;

abstract class Constants {
	public const SCHEMA_VERSION_KEY                   = '2fa-wa-schema-version';
	public const OPTIONS_KEY                          = '2fa_webauthn_settings';
	public const REGISTRATION_CONTEXT_USER_META_KEY   = '_webauthn_registration_context';
	public const AUTHENTICATION_CONTEXT_USER_META_KEY = '_webauthn_auth_context';
	public const WA_CREDENTIALS_TABLE_NAME            = '2fa_webauthn_credentials';
	public const WA_USERS_TABLE_NAME                  = '2fa_webauthn_users';
}
