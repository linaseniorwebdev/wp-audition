<?php

/**
 * Audition Multisite Hooks
 *
 * @package Audition
 * @subpackage Multisite
 */

/**
 * Add Actions
 */

// Actions and Forms
add_action('init', 'audition_ms_register_default_actions', 0);
add_action('init', 'audition_ms_register_default_forms',   0);

// Registration
add_action('wpmu_activate_user', 'audition_handle_auto_login', 10, 2);
add_action('wpmu_activate_blog', 'audition_handle_auto_login', 10, 2);

/**
 * Add Filters
 */

// Shortcodes
add_filter('audition_shortcode', 'audition_ms_filter_signup_shortcode',     10, 3);
add_filter('audition_shortcode', 'audition_ms_filter_activation_shortcode', 10, 3);

// URLs
add_filter('network_site_url', 'audition_filter_site_url', 10, 3);

// Passwords
add_filter('wp_pre_insert_user_data',   'audition_ms_filter_pre_insert_user_data', 10, 1);
add_filter('update_welcome_email',      'audition_ms_filter_welcome_email',        10, 4);
add_filter('update_welcome_user_email', 'audition_ms_filter_welcome_user_email',   10, 3);
