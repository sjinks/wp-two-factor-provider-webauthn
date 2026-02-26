<?php defined( 'ABSPATH' ) || die(); ?>
<p><?php esc_html_e( 'Please insert (and tap) your security key.', 'two-factor-provider-webauthn' ); ?></p>
<p>&nbsp;</p>
<p id="webauthn-retry" style="text-align: center;">
	<button type="button" class="button button-secondary"><?php esc_html_e( 'Retry', 'two-factor-provider-webauthn' ); ?></button>
</p>
<input type="hidden" name="webauthn_response" id="webauthn_response" />
