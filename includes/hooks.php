<?php

/**
 * Audition Hooks
 *
 * @package Audition
 * @subpackage Core
 */

/**
 * Add Actions
 */

// Actions and Forms
add_action('init', 'audition_register_default_actions', 0);
add_action('init', 'audition_register_default_forms',   0);

// Rewrite
add_action('init', 'audition_add_rewrite_tags' );
add_action('init', 'audition_add_rewrite_rules');

// Widgets
add_action('widgets_init', 'Audition_Widget::register');


// Request
add_action('parse_request', 'audition_parse_request');

// Query
add_action('parse_query', 'audition_parse_query');

// Pages
add_action('wp', 'audition_remove_default_actions_and_filters');

// Template
add_action('template_redirect',  'audition_action_handler',   0);
add_action('wp_enqueue_scripts', 'audition_enqueue_styles',  10);
add_action('wp_enqueue_scripts', 'audition_enqueue_scripts', 10);
add_action('wp_head',            'audition_do_login_head',   10);

// Registration
add_action('pre_user_login',    'audition_set_user_login'       );
add_action('register_new_user', 'audition_set_new_user_password');
add_action('register_new_user', 'audition_handle_auto_login'    );

add_action('register_new_user',      'audition_send_new_user_notifications', 10, 1);
add_action('edit_user_created_user', 'audition_send_new_user_notifications', 10, 2);

remove_action('register_new_user',      'wp_send_new_user_notifications');
remove_action('edit_user_created_user', 'wp_send_new_user_notifications');

// Passwords
add_action('retrieved_password_key', 'audition_retrieve_password_notification', 10, 2);

// Activation
add_action('audition_activate', 'audition_flush_rewrite_rules');
add_action('audition_activate_extension', 'audition_get_extension_data');

// Deactivation
add_action('audition_deactivate', 'audition_flush_rewrite_rules');
add_action('audition_deactivate_extension', 'audition_get_extension_data');

/**
 * Add Filters
 */

// Pages
add_filter('the_posts',          'audition_the_posts',                 10, 2);
add_filter('page_template',      'audition_page_template',             10, 3);
add_filter('body_class',         'audition_body_class',                10, 2);
add_filter('get_edit_post_link', 'audition_filter_get_edit_post_link', 10, 2);
add_filter('comments_array',     'audition_filter_comments_array',     10, 1);

// URLs
add_filter('site_url',         'audition_filter_site_url',         10, 3);
add_filter('network_site_url', 'audition_filter_site_url',         10, 3);
add_filter('logout_url',       'audition_filter_logout_url',       10, 2);
add_filter('lostpassword_url', 'audition_filter_lostpassword_url', 10, 2);

// Authentication
add_filter('authenticate', 'audition_enforce_login_type', 20, 3);
if (audition_is_username_login_type()) {
	remove_filter('authenticate', 'wp_authenticate_email_password', 20);
} elseif (audition_is_email_login_type()) {
	remove_filter('authenticate', 'wp_authenticate_username_password', 20);
}

// Registration
if (! audition_is_wp_login()) {
	add_filter('registration_errors', 'audition_validate_new_user_password', 10);
}
add_filter('audition_registration_redirect', 'audition_registration_redirect', 10, 2);

// Notifications
add_filter('wp_new_user_notification_email', 'audition_add_password_notice_to_new_user_notification_email');

// Customizer
add_filter('customize_nav_menu_available_item_types', 'audition_filter_customize_nav_menu_available_item_types', 10, 1);
add_filter('customize_nav_menu_available_items',      'audition_filter_customize_nav_menu_available_items',      10, 4);

// Nav menus
add_filter('wp_setup_nav_menu_item', 'audition_setup_nav_menu_item'      );
add_filter('nav_menu_css_class',     'audition_nav_menu_css_class', 10, 2);

// Extensions
add_filter('plugins_api',                           'audition_add_extension_data_to_plugins_api',       10, 3);
add_filter('pre_set_site_transient_update_plugins', 'audition_add_extension_data_to_plugins_transient', 10, 1);
