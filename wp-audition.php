<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/linaseniorwebdev
 * @since             1.0.0
 * @package           Wp_Audition
 *
 * @wordpress-plugin
 * Plugin Name:       WP Audition
 * Plugin URI:        wp-audition
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Top Service
 * Author URI:        https://github.com/linaseniorwebdev
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-audition
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WP_AUDITION_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-audition-activator.php
 */
function activate_wp_audition() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-audition-activator.php';
	Wp_Audition_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-audition-deactivator.php
 */
function deactivate_wp_audition() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-audition-deactivator.php';
	Wp_Audition_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_audition' );
register_deactivation_hook( __FILE__, 'deactivate_wp_audition' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-audition.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_audition() {

	$plugin = new Wp_Audition();
	$plugin->run();

}
run_wp_audition();
