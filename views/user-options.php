<?php defined( 'ABSPATH' ) || die(); ?>
<p>
	<?php echo wp_kses_post( __( 'Requires an HTTPS connection. Please configure your security keys in the <a href="#webauthn-security-keys-section">Security Keys (WebAuthn)</a> section below.', 'two-factor-provider-webauthn' ) ); ?>
</p>
