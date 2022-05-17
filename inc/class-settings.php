<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn;

use ArrayAccess;
use LogicException;
use WildWolf\Utils\Singleton;

/**
 * @psalm-type SettingsArray = array{
 *  authenticator_attachment: string,
 *  user_verification_requirement: string,
 *  timeout: int,
 *  u2f_hack: bool,
 *  disable_u2f: bool,
 * }
 *
 * @template-implements ArrayAccess<string, scalar>
 */
final class Settings implements ArrayAccess {
	use Singleton;

	/** @var string  */
	public const OPTIONS_KEY = Constants::OPTIONS_KEY;

	/**
	 * @psalm-readonly
	 * @psalm-var SettingsArray
	 */
	private static array $defaults = [
		'authenticator_attachment'      => '',
		'user_verification_requirement' => 'preferred',
		'timeout'                       => 0,
		'u2f_hack'                      => true,
		'disable_u2f'                   => false,
	];

	/**
	 * @var array
	 * @psalm-var SettingsArray
	 */
	private $options;

	private function __construct() {
		$this->refresh();
	}

	public function refresh(): void {
		/** @var mixed */
		$settings      = get_option( self::OPTIONS_KEY );
		$this->options = SettingsValidator::ensure_data_shape( is_array( $settings ) ? $settings : [] );
	}

	/**
	 * @psalm-return SettingsArray
	 */
	public static function defaults(): array {
		return self::$defaults;
	}

	/**
	 * @param mixed $offset
	 */
	public function offsetExists( $offset ): bool {
		return isset( $this->options[ (string) $offset ] );
	}

	/**
	 * @param mixed $offset
	 * @return int|string|null|bool
	 */
	#[\ReturnTypeWillChange]
	public function offsetGet( $offset ) {
		return $this->options[ (string) $offset ] ?? null;
	}

	/**
	 * @param mixed $_offset
	 * @param mixed $_value
	 * @psalm-return never
	 * @throws LogicException
	 */
	public function offsetSet( $_offset, $_value ): void {
		throw new LogicException();
	}

	/**
	 * @param mixed $_offset
	 * @psalm-return never
	 * @throws LogicException
	 */
	public function offsetUnset( $_offset ): void {
		throw new LogicException();
	}

	/**
	 * @psalm-return SettingsArray
	 */
	public function as_array(): array {
		return $this->options;
	}

	/**
	 * @return string
	 */
	public function get_authenticator_attachment(): string {
		return $this->options['authenticator_attachment'];
	}

	public function get_user_verification_requirement(): string {
		return $this->options['user_verification_requirement'];
	}

	public function get_timeout(): int {
		return $this->options['timeout'];
	}

	public function get_u2f_hack(): bool {
		return $this->options['u2f_hack'];
	}

	public function get_disable_u2f(): bool {
		return $this->options['disable_u2f'];
	}
}
