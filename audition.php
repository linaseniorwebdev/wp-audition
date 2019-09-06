<?php

/*
Plugin Name: Audition Plugin for Wordpress
Plugin URI: https://github.com/linaseniorwebdev
Description: Helps to create matching site with Wordpress
Version: 1.0.0
Author: Top Service
Author URI: https://github.com/linaseniorwebdev
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: audition
Network: true
*/

define( 'AUDITION_VERSION', '1.0.0' );
define( 'AUDITION_PATH', plugin_dir_path( __FILE__ ) );
define( 'AUDITION_URL',  plugin_dir_url( __FILE__ ) );

require AUDITION_PATH . 'includes/class-audition.php';
require AUDITION_PATH . 'includes/class-audition-action.php';
require AUDITION_PATH . 'includes/class-audition-form.php';
require AUDITION_PATH . 'includes/class-audition-form-field.php';
require AUDITION_PATH . 'includes/class-audition-widget.php';
require AUDITION_PATH . 'includes/actions.php';
require AUDITION_PATH . 'includes/forms.php';
require AUDITION_PATH . 'includes/compat.php';
require AUDITION_PATH . 'includes/functions.php';
require AUDITION_PATH . 'includes/options.php';
require AUDITION_PATH . 'includes/shortcodes.php';
require AUDITION_PATH . 'includes/hooks.php';

if ( is_multisite() ) {
	require AUDITION_PATH . 'includes/ms-functions.php';
	require AUDITION_PATH . 'includes/ms-hooks.php';
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require AUDITION_PATH . 'includes/commands.php';
}

audition();

if ( is_admin() ) {
	require AUDITION_PATH . 'admin/class-audition-admin.php';
	require AUDITION_PATH . 'admin/functions.php';
	require AUDITION_PATH . 'admin/settings.php';
	require AUDITION_PATH . 'admin/hooks.php';

	audition_admin();
}
