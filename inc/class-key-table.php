<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn;

use WP_List_Table;
use WP_User;

/**
 * @psalm-import-type CredentialRow from WebAuthn_Credential_Store
 * @psalm-property CredentialRow[] $items
 * @psalm-suppress PropertyNotSetInConstructor
 */
class Key_Table extends WP_List_Table {
	private WP_User $user;

	public function __construct( WP_User $user ) {
		parent::__construct( [
			'screen' => 'webauthn-keys',
		] );

		$this->user = $user;
	}

	/**
	 * @return void
	 */
	public function prepare_items() {
		$this->items = WebAuthn_Credential_Store::get_user_keys( $this->user );
	}

	public function get_columns(): array {
		return [
			'name'      => esc_html__( 'Name', 'two-factor-provider-webauthn' ),
			'counter'   => esc_html__( 'Counter', 'two-factor-provider-webauthn' ),
			'added'     => esc_html__( 'Added', 'two-factor-provider-webauthn' ),
			'last_used' => esc_html__( 'Last Used', 'two-factor-provider-webauthn' ),
		];
	}

	/**
	 * @param string $which
	 * @return void
	 */
	protected function display_tablenav( $which ) {
		/* Do nothing */
	}

	/**
	 * @psalm-param CredentialRow $item
	 */
	protected function column_name( $item ): string {
		$actions = [
			'rename hide-if-no-js' => sprintf(
				'<a href="#" data-handle="%1$s" data-nonce="%2$s">%3$s</a>',
				$item->credential_id,
				wp_create_nonce( 'rename-key_' . $item->credential_id ),
				__( 'Rename', 'two-factor-provider-webauthn' )
			),
			'delete hide-if-no-js' => sprintf(
				'<a href="#" data-handle="%1$s" data-nonce="%2$s">%3$s</a>',
				$item->credential_id,
				wp_create_nonce( 'delete-key_' . $item->credential_id ),
				__( 'Revoke', 'two-factor-provider-webauthn' )
			),
		];

		return '<span class="key-name">' . esc_html( $item->name ) . '</span>' . $this->row_actions( $actions );
	}

	/**
	 * @psalm-param CredentialRow $item
	 */
	protected function column_counter( $item ): string {
		return number_format_i18n( (int) $item->counter, 0 );
	}

	/**
	 * @psalm-param CredentialRow $item
	 */
	protected function column_added( $item ): string {
		return esc_html( DateTimeUtils::format_date_time( (int) $item->added ) );
	}

	/**
	 * @psalm-param CredentialRow $item
	 */
	protected function column_last_used( $item ): string {
		return esc_html( DateTimeUtils::format_date_time( (int) $item->last_used ) );
	}
}
