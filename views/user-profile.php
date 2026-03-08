<?php

use WildWolf\WordPress\TwoFactorWebAuthn\Key_Table;

defined( 'ABSPATH' ) || die(); 

/** @psalm-var array{user: WP_User} $params */
?>

<p>
	<?php esc_html_e( 'Requires an HTTPS connection. Please configure your security keys below.', 'two-factor-provider-webauthn' ); ?>
</p>

<div id="webauthn-security-keys-section">
	<noscript>
		<div class="notice inline notice-error">
			<p><?php esc_html_e( 'You need to enable JavaScript to manage security keys.', 'two-factor-provider-webauthn' ); ?></p>
		</div>
	</noscript>

<?php if ( ! is_ssl() ) : ?>
	<div class="notice inline notice-error">
		<p>
			<?php esc_html_e( 'WebAuthn requires an HTTPS connection. You will be unable to add new security keys over HTTP.', 'two-factor-provider-webauthn' ); ?>
		</p>
	</div>
<?php else : ?>

	<div class="hide-if-no-js add-webauthn-key">
		<p>
			<label for="webauthn-key-name"><strong><?php esc_html_e( 'Key name:', 'two-factor-provider-webauthn' ); ?></strong></label>
			<input type="text" id="webauthn-key-name" value="" maxlength="255" />
			<button type="button" class="button button-secondary"><?php echo esc_html( _x( 'Register New Key', 'security key', 'two-factor-provider-webauthn' ) ); ?></button>
		</p>
		<span class="security-key-status" aria-live="polite"></span>
	</div>
<?php endif; ?>

	<div class="registered-keys">
<?php
$table = new Key_Table( $params['user'] );
$table->prepare_items();
$table->display();
?>
		<p class="description">
			<?php esc_html_e( 'If the counter is always 0, that is often normal.', 'two-factor-provider-webauthn' ); ?><br/>
			<?php esc_html_e( 'If it was non-zero before and then stopped increasing or reversed, that is more suspicious and points to an authenticator malfunction or clone/race scenario.', 'two-factor-provider-webauthn' ); ?>
		</p>
	</div>
</div>
<script type="text/x-template" id="webauthn-no-keys">
	<tr class="no-items">
		<td class="colspanchange" colspan="<?php echo (int) $table->get_column_count(); ?>">
			<?php $table->no_items(); ?>
		</td>
	</tr>
</script>
<script type="text/x-template" id="webauthn-revoke-confirm">
	<div class="confirm-revoke" role="alert">
		<p><?php esc_html_e( 'Are you sure to revoke this key?', 'two-factor-provider-webauthn' ); ?></p>
		<button type="button" class="button button-link-delete"><?php esc_html_e( 'Yes' ); ?></button>
		<button type="button" class="button button-secondary"><?php esc_html_e( 'No' ); ?></button>
	</div>
</script>
<script type="text/x-template" id="webauthn-rename-key">
	<div class="rename-key">
		<p>
			<label>
				<?php esc_html_e( 'New name:', 'two-factor-provider-webauthn' ); ?><br/>
				<input type="text" value=""/>
			</label>
		</p>
		<button type="button" class="button button-primary"><?php esc_html_e( 'OK' ); ?></button>
		<button type="button" class="button button-secondary"><?php esc_html_e( 'Cancel' ); ?></button>
	</div>
</script>
