<?php

/**
 * Audition Admin Hooks
 *
 * @package Audition
 * @subpackage Administration
 */

/**
 * Add actions
 */

// General
add_action('admin_enqueue_scripts', 'audition_admin_enqueue_style_and_scripts');

// Notices
add_action('admin_notices',              'audition_admin_notices'            );
add_action('wp_ajax_audition-dismiss-notice', 'audition_admin_ajax_dismiss_notice');

// Settings
if (is_multisite()) {
	add_action('network_admin_menu', 'audition_admin_add_menu_items'   );
	add_action('admin_init',         'audition_admin_register_settings');
	add_action('admin_init',         'audition_admin_save_ms_settings' );
} else {
	add_action('admin_menu', 'audition_admin_add_menu_items'   );
	add_action('admin_init', 'audition_admin_register_settings');
}
add_action('current_screen', 'audition_admin_add_settings_help_tabs');

// Update
add_action('admin_init', 'audition_admin_update');

// Nav menus
add_action('admin_head-nav-menus.php', 'audition_admin_add_nav_menu_meta_box', 10);

/**
 * Add filters
 */

// General
add_filter('plugin_action_links', 'audition_admin_filter_plugin_action_links', 10, 4);

// Nav menus
add_filter('wp_edit_nav_menu_walker',  'audition_admin_filter_edit_nav_menu_walker', 99);
