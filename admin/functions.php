<?php

/**
 * Audition Admin Functions
 */

/**
 * Get the Audition Admin instance.
 *
 * @since 7.0
 *
 * @return Audition_Admin
 */
function audition_admin() {
	return Audition_Admin::get_instance();
}

/**
* Determine if the current page is a TML page.
*
* @since 7.0
*
* @param string $page The page name.
* @return boolean True if the current page is the specified page, false if not.
*/
function audition_admin_is_plugin_page($page = '') {
	global $plugin_page;

	if (! empty($page)) {
		return ("audition-$page" == $plugin_page);
	}

	return (strpos($plugin_page, 'audition') === 0);
}

/**
 * Add an admin page.
 *
 * @since 7.0
 *
 * @see Audition_Admin::add_menu_item()
 *
 * @param array|Audition_Extension $args
 */
function audition_admin_add_menu_item($args = array()) {
	audition_admin()->add_menu_item($args);
}

/**
 * Register the admin menus.
 *
 * @since 7.0
 */
function audition_admin_add_menu_items() {

	// Bail if multisite and not in the network admin
	if (is_multisite() && ! is_network_admin()) {
		return;
	}

	$audition_admin = audition_admin();

	// Add the main menu item
	$audition_admin->add_menu_item(array(
		'page_title'  => esc_html__('Audition Settings', 'audition'),
		'menu_title'  => esc_html__('Audition',          'audition'),
		'menu_slug'   => 'audition',
		'menu_icon'   => 'data:image/svg+xml;base64,' . base64_encode(
			file_get_contents(AUDITION_PATH . 'admin/assets/images/logo.svg')
		),
		'parent_slug' => false,
	));

	// Add the submenu item
	$audition_admin->add_menu_item(array(
		'page_title'  => esc_html__('Audition Settings', 'audition'),
		'menu_title'  => esc_html__('General',                 'audition'),
		'menu_slug'   => 'audition',
		'parent_slug' => 'audition',
	));

	$has_licenses = false;

	if ($has_licenses) {
		// Add the licenses menu item
		$audition_admin->add_menu_item(array(
			'page_title'  => esc_html__('Audition Licenses', 'audition'),
			'menu_title'  => esc_html__('Licenses',                'audition'),
			'menu_slug'   => 'audition-licenses',
			'parent_slug' => 'audition',
		));
		add_settings_section('audition_settings_licenses', '', '__return_null', 'audition-licenses');
	}
}

/**
* Enqueue admin scripts.
*
* @since 7.0
*/
function audition_admin_enqueue_style_and_scripts() {
	$suffix = SCRIPT_DEBUG ? '' : '.min';

	wp_enqueue_style('audition-admin', AUDITION_URL . "admin/assets/styles/audition-admin$suffix.css", array(), AUDITION_VERSION);
	wp_enqueue_script('audition-admin', AUDITION_URL . "admin/assets/scripts/audition-admin$suffix.js", array('jquery', 'postbox'), AUDITION_VERSION);
	wp_localize_script('audition-admin', 'tmlAdmin', array(
		'interimLoginUrl' => site_url(add_query_arg(array(
			'interim-login' => 1,
			'wp_lang'       => get_user_locale(),
		), 'wp-login.php'), 'login'),
	));
}

/**
 * Display admin notices.
 *
 * @since 7.0
 */
function audition_admin_notices() {
	global $plugin_page;

	$screen = get_current_screen();

	// Bail if not on Dashboard or a TML page
	if ('dashboard' != $screen->id && 'audition' != $screen->parent_base) {
		return;
	}

	// Bail if the user cannot activate plugins
	if (! current_user_can('activate_plugins')) {
		return;
	}
}

/**
 * Handle saving of notice dismissals.
 *
 * @since 7.0.8
 */
function audition_admin_ajax_dismiss_notice() {
	if (empty($_POST['notice'])) {
		return;
	}
	$dismissed_notices = get_site_option('_audition_dismissed_notices', array());
	$dismissed_notices[] = sanitize_key($_POST['notice']);
	update_site_option('_audition_dismissed_notices', $dismissed_notices);
	wp_send_json_success();
}

/**
 * Update TML.
 *
 * @since 7.0
 */
function audition_admin_update() {
	$version = audition_get_installed_version();

	// Bail if no update needed
	if (version_compare($version, AUDITION_VERSION, '>=')) {
		return;
	}

	// 7.0 upgrade
	if (version_compare($version, '7.0', '<')) {
		// Initial migration
		$options = get_option('audition', array());
		if (! empty($options)) {
			if (! empty($options['login_type'])) {
				update_site_option('audition_login_type', $options['login_type']);
			}
			delete_option('audition');
		}
	}

	// Set the first time install date
	if (! get_site_option('_audition_installed_at')) {
		update_site_option('_audition_installed_at', current_time('timestamp'));
	}

	// Set the update date
	update_site_option('_audition_updated_at', current_time('timestamp'));

	// Store the previous version
	if (! empty($version)) {
		update_site_option('_audition_previous_version', $version);
	}

	// Bump the installed version
	update_site_option('_audition_version', AUDITION_VERSION);

	// Force permalinks to be regenerated
	audition_flush_rewrite_rules();
}

/**
 * Sanitize a slug.
 *
 * @since 7.0
 *
 * @param string $slug The slug.
 * @return string The slug.
 */
function audition_sanitize_slug($slug) {
	if (! empty($slug)) {
		$slug = preg_replace('#/+#', '/', '/' . str_replace('#', '', $slug));
		$slug = trim(preg_replace('|^/index\.php/|', '', $slug), '/');
	}
	return $slug;
}

/**
 * Add the nav menu meta box.
 *
 * @since 7.0
 */
function audition_admin_add_nav_menu_meta_box() {
	add_meta_box('audition_actions',
		__('Audition Actions', 'audition'),
		'audition_admin_nav_menu_meta_box',
		'nav-menus',
		'side',
		'default'
	);
}

/**
 * Render the nav menu meta box.
 *
 * @since 7.0
 */
function audition_admin_nav_menu_meta_box() {
	global $_nav_menu_placeholder, $nav_menu_selected_id;

	$_nav_menu_placeholder = 0 > $_nav_menu_placeholder ? $_nav_menu_placeholder - 1 : -1;

	$actions = wp_list_filter(audition_get_actions(), array(
		'show_in_nav_menus' => true
	));
	?>

	<div id="audition-action" class="posttypediv">
		<div class="tabs-panel tabs-panel-active">
			<ul class="categorychecklist form-no-clear">
				<?php echo walk_nav_menu_tree(array_map('wp_setup_nav_menu_item', $actions), 0, (object) array(
					'walker' => new Walker_Nav_Menu_Checklist(),
				)); ?>
			</ul>
		</div>
		<p class="button-controls wp-clearfix">
			<span class="add-to-menu">
				<input type="submit"<?php wp_nav_menu_disabled_check($nav_menu_selected_id); ?> class="button submit-add-to-menu right" value="<?php esc_attr_e('Add to Menu'); ?>" name="add-audition-action-menu-item" id="submit-audition-action" />
				<span class="spinner"></span>
			</span>
		</p>
	</div>

	<?php
}

/**
 * Filter the edit nav menu walker.
 *
 * @since 7.0
 *
 * @param string $walker The name of the walker class.
 * @return string The name of the walker class.
 */
function audition_admin_filter_edit_nav_menu_walker($walker)  {
	$walker = 'Audition_Walker_Nav_Menu_Edit';
	if (! class_exists($walker)) {
		require_once AUDITION_PATH . 'admin/class-audition-walker-nav-menu-edit.php';
	}
	return $walker;
}

/**
 * Filter the plugin action links.
 *
 * @since 7.0.12
 *
 * @param array  $actions The plugin action links.
 * @param string $file    The path to the plugin file.
 * @param array  $data    The plugin data.
 * @param string $context The plugin context.
 * @return array The plugin action links.
 */
function audition_admin_filter_plugin_action_links($actions, $file, $data, $context) {
	if ('audition/audition.php' == $file) {
		$actions['settings'] = sprintf('<a href="%1$s">%2$s</a>',
			admin_url('admin.php?page=audition'),
			__('Settings')
		);
	}
	return $actions;
}
