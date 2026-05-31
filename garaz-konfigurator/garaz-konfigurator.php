<?php
/**
 * Plugin Name:       Garaż Konfigurator
 * Description:       Interaktywny konfigurator garażu blaszanego z wizualizacją 3D, wyceną, eksportem PDF i wysyłką e-mailem.
 * Version:           1.0.0
 * Author:            Bartosz Marek
 * Text Domain:       garaz-konfigurator
 * Requires at least: 6.0
 * Requires PHP:      8.0
 */

defined( 'ABSPATH' ) || exit;

define( 'GARAZ_VERSION',    '1.0.0' );
define( 'GARAZ_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'GARAZ_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once GARAZ_PLUGIN_DIR . 'includes/class-configurator.php';
require_once GARAZ_PLUGIN_DIR . 'includes/class-email-handler.php';

add_action( 'plugins_loaded', function () {
	new Garaz_Configurator();
	new Garaz_Email_Handler();
} );
