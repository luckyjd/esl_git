<?php

/**
 * Class Thim_Free_Theme
 *
 * @since 1.3.0
 */
class Thim_Free_Theme extends Thim_Singleton {

	/**
	 * Check is free theme.
	 *
	 * @since 1.3.0
	 *
	 * @return bool
	 */
	public static function is_free() {
		return ! ! get_theme_support( 'thim-core-lite' );
	}

	/**
	 * Thim_Free_Theme constructor.
	 *
	 * @since 1.3.0
	 */
	protected function __construct() {
		$this->hooks();
	}

	/**
	 * Add hooks.
	 *
	 * @since 1.3.0
	 */
	private function hooks() {

	}
}