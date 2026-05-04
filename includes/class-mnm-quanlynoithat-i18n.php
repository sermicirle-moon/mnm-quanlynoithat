<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://1234
 * @since      1.0.0
 *
 * @package    Mnm_Quanlynoithat
 * @subpackage Mnm_Quanlynoithat/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Mnm_Quanlynoithat
 * @subpackage Mnm_Quanlynoithat/includes
 * @author     Xenlulozo <dthanhdat859@gmail.com>
 */
class Mnm_Quanlynoithat_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'mnm-quanlynoithat',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
