<?php

/**
 * Audition Admin Settings
 */

/**
 * Register the settings.
 */
function audition_admin_register_settings() {

	$settings = array(
		'audition' =>array(
			'sections' => audition_admin_get_settings_sections(),
			'fields'   => audition_admin_get_settings_fields(),
		),
	);

	// Loop through settings
	foreach ($settings as $group) {

		// Loop through sections
		foreach ($group['sections'] as $section_id => $section) {

			// Only add section and fields if section has fields
			if (empty($group['fields'][ $section_id ])) {
				continue;
			}

			$page = ! empty($section['page']) ? $section['page'] : 'audition';

			// Add the section
			add_settings_section($section_id, $section['title'], $section['callback'], $page);

			// Loop through fields for this section
			foreach ($group['fields'][ $section_id ] as $field_id => $field) {

				// Add the field
				if (! empty($field['callback']) && ! empty($field['title'])) {
					add_settings_field($field_id, $field['title'], $field['callback'], $page, $section_id, isset($field['args']) ? $field['args'] : array());
				}

				// Register the setting
				register_setting($page, $field_id, $field['sanitize_callback']);
			}
		}
	}
}

/**
 * Get the settings sections.
 */
function audition_admin_get_settings_sections() {
	return (array) apply_filters('audition_admin_get_settings_sections', array(
		'audition_settings_login' => array(
			'title'    => __('Log In'),
			'callback' => '__return_null',
			'page'     => 'audition',
		),
		'audition_settings_registration' => array(
			'title'    => __('Registration', 'audition'),
			'callback' => '__return_null',
			'page'     => 'audition',
		),
		'audition_settings_slugs' => array(
			'title'    => __('Slugs', 'audition'),
			'callback' => 'audition_admin_setting_callback_slugs_section',
			'page'     => 'audition',
		),
	));
}

/**
 * Get the settings fields.
 */
function audition_admin_get_settings_fields() {
	$fields = array();

	// Login
	$fields['audition_settings_login'] = array(
		// Login type
		'audition_login_type' => array(
			'title'             => __('Login Type', 'audition'),
			'callback'          => 'audition_admin_setting_callback_radio_group_field',
			'sanitize_callback' => 'sanitize_text_field',
			'args' => array(
				'label_for' => 'audition_login_type',
				'legend'    => __('Login Type', 'audition'),
				'options'   => array(
					'default'  => __('Default',       'audition'),
					'username' => __('Username only', 'audition'),
					'email'    => __('Email only',    'audition'),
				),
				'checked'   => get_site_option('audition_login_type', 'default'),
			),
		),
	);

	// Registration
	$fields['audition_settings_registration'] = array(
		// Registration type
		'audition_registration_type' => array(
			'title'             => __('Registration Type', 'audition'),
			'callback'          => 'audition_admin_setting_callback_radio_group_field',
			'sanitize_callback' => 'sanitize_text_field',
			'args' => array(
				'label_for' => 'audition_registration_type',
				'legend'    => __('Registration Type', 'audition'),
				'options'   => array(
					'default'  => __('Default',    'audition'),
					'email'    => __('Email only', 'audition'),
				),
				'checked'   => get_site_option('audition_registration_type', 'default'),
			),
		),
		// User passwords
		'audition_user_passwords' => array(
			'title'             => __('Passwords', 'audition'),
			'callback'          => 'audition_admin_setting_callback_checkbox_field',
			'sanitize_callback' => 'sanitize_text_field',
			'args' => array(
				'label_for' => 'audition_user_passwords',
				'label'     => __('Allow users to set their own password', 'audition'),
				'value'     => '1',
				'checked'   => get_site_option('audition_user_passwords'),
			),
		),
		// Auto-login
		'audition_auto_login' => array(
			'title'             => __('Auto-Login', 'audition'),
			'callback'          => 'audition_admin_setting_callback_checkbox_field',
			'sanitize_callback' => 'sanitize_text_field',
			'args' => array(
				'label_for' => 'audition_auto_login',
				'label'     => __('Automatically log in users after registration', 'audition'),
				'value'     => '1',
				'checked'   => get_site_option('audition_auto_login'),
			),
		),
	);

	// Slugs
	$fields['audition_settings_slugs'] = array();
	foreach (audition_get_actions() as $action) {
		if (! $action->show_in_slug_settings) {
			continue;
		}

		$slug_option = 'audition_' . $action->get_name() . '_slug';

		$fields['audition_settings_slugs'][ $slug_option ] = array(
			'title'             => $action->get_title(),
			'callback'          => 'audition_admin_setting_callback_input_field',
			'sanitize_callback' => 'sanitize_text_field',
			'args' => array(
				'label_for'   => $slug_option,
				'value'       => get_site_option($slug_option, $action->get_slug()),
				'input_class' => 'regular-text code',
				'description' => sprintf('<a href="%1$s">%1$s</a>', $action->get_url()),
			),
		);
	}

	/**
	 * Filters the settings fields.
	 */
	return (array) apply_filters('audition_admin_get_settings_fields', $fields);
}

/**
 * Render the "Slugs" section.
 */
function audition_admin_setting_callback_slugs_section() {
?>

<p><?php esc_html_e('The slugs defined here will be used to generate the URL to the corresponding action. You can see this URL below the slug field. If you would like to use pages for these actions, simply make sure the slug for the action below matches the slug of the page you would like to use for that action.', 'audition'); ?></p>

<?php
}

/**
 * Render a text setting field.
 */
function audition_admin_setting_callback_input_field($args) {
	$args = wp_parse_args($args, array(
		'label_for'   => '',
		'value'       => '',
		'description' => '',
		'input_type'  => 'text',
		'input_class' => 'regular-text',
	));
?>

	<input type="<?php echo esc_attr($args['input_type']); ?>" name="<?php echo esc_attr($args['label_for']); ?>" id="<?php echo esc_attr($args['label_for']); ?>" value="<?php echo esc_attr($args['value']); ?>" class="<?php echo esc_attr($args['input_class']); ?>" />

	<?php if (! empty($args['description'])) : ?>
		<p class="description"><?php echo $args['description']; ?></p>
	<?php endif; ?>

<?php
}

/**
 * Render a checkbox setting field.
 */
function audition_admin_setting_callback_checkbox_field($args) {
	$args = wp_parse_args($args, array(
		'label_for'   => '',
		'label'       => '',
		'value'       => '1',
		'checked'     => '',
		'description' => '',
	));
?>

	<input type="checkbox" name="<?php echo esc_attr($args['label_for']); ?>" id="<?php echo esc_attr($args['label_for']); ?>" value="<?php echo $args['value']; ?>" <?php checked(! empty($args['checked'])); ?> /> <label for="<?php echo esc_attr($args['label_for']); ?>"><?php echo esc_html($args['label']); ?></label>

	<?php if (! empty($args['description'])) : ?>
		<p class="description"><?php echo $args['description']; ?></p>
	<?php endif; ?>

<?php
}

/**
 * Render a radio group setting field.
 */
function audition_admin_setting_callback_checkbox_group_field($args) {
	$args = wp_parse_args($args, array(
		'legend'      => '',
		'options'     => array(),
		'description' => '',
	));

	$options = array();
	foreach ((array) $args['options'] as $option_name => $option) {
		$options[] = sprintf(
			'<label><input type="checkbox" name="%1$s" value="%2$s"%3$s> %4$s</label>',
			esc_attr($option_name),
			esc_attr($option['value']),
			checked(! empty($option['checked']), true, false),
			esc_html($option['label'])
		);
	}
?>

	<fieldset>
		<legend class="screen-reader-text"><span><?php echo esc_html($args['legend']); ?></span></legend>
		<?php echo implode("<br />\n", $options); ?>
	</fieldset>

	<?php if (! empty($args['description'])) : ?>
		<p class="description"><?php echo $args['description']; ?></p>
	<?php endif; ?>

<?php
}

/**
 * Render a dropdown setting field.
 */
function audition_admin_setting_callback_dropdown_field($args) {
	$args = wp_parse_args($args, array(
		'label_for'   => '',
		'options'     => array(),
		'selected'    => '',
		'description' => '',
	));

	$options = array();
	foreach ((array) $args['options'] as $value => $label) {
		$options[] = sprintf(
			'<option value="%1$s"%2$s>%3$s</option>',
			esc_attr($value),
			selected($args['selected'], $value, false),
			esc_html($label)
		);
	}
?>

	<select name="<?php echo esc_attr($args['label_for']); ?>" id="<?php echo esc_attr($args['label_for']); ?>">
		<?php echo implode("<br />\n", $options); ?>
	</select>

	<?php if (! empty($args['description'])) : ?>
		<p class="description"><?php echo $args['description']; ?></p>
	<?php endif; ?>

<?php
}

/**
 * Render a radio group setting field.
 */
function audition_admin_setting_callback_radio_group_field($args) {
	$args = wp_parse_args($args, array(
		'label_for'   => '',
		'legend'      => '',
		'options'     => array(),
		'checked'     => '',
		'description' => '',
	));

	$options = array();
	foreach ((array) $args['options'] as $value => $label) {
		$options[] = sprintf(
			'<label><input type="radio" name="%1$s" value="%2$s"%3$s> %4$s</label>',
			esc_html($args['label_for']),
			esc_attr($value),
			checked($args['checked'], $value, false),
			esc_html($label)
		);
	}
?>

	<fieldset>
		<legend class="screen-reader-text"><span><?php echo esc_html($args['legend']); ?></span></legend>
		<?php echo implode("<br />\n", $options); ?>
	</fieldset>

	<?php if (! empty($args['description'])) : ?>
		<p class="description"><?php echo $args['description']; ?></p>
	<?php endif; ?>

<?php
}

/**
 * Render the settings page.
 */
function audition_admin_settings_page() {
	global $title, $plugin_page;

	if ('audition' == $plugin_page) {
		audition_flush_rewrite_rules();
	}

	settings_errors();
?>

<div class="wrap">
	<h1><?php echo esc_html($title) ?></h1>
	<hr class="wp-header-end">

	<form id="audition-settings" action="<?php echo is_network_admin() ? '' : 'options.php'; ?>" method="post">

		<?php settings_fields($plugin_page); ?>

		<?php do_settings_sections($plugin_page); ?>

		<?php submit_button(); ?>
	</form>
</div>

<?php
}

/**
 * Handle the network settings page.
 */
function audition_admin_save_ms_settings() {

	if (! audition_is_post_request()) {
		return;
	}

	$action      = isset($_REQUEST['action']     ) ? $_REQUEST['action']      : '';
	$option_page = isset($_REQUEST['option_page']) ? $_REQUEST['option_page'] : '';

	if (! audition_admin()->has_page($option_page)) {
		return;
	}

	/* This filter is documented in wp-admin/options.php */
	$whitelist_options = apply_filters('whitelist_options', array());

	if (! isset($whitelist_options[ $option_page ])) {
		wp_die(__('<strong>ERROR</strong>: options page not found.'));
	}

	foreach ($whitelist_options[ $option_page ] as $option) {
		$option = trim($option);
		$value  = null;
		if (isset($_POST[ $option ])) {
			$value = $_POST[ $option ];
			if (! is_array($value)) {
				$value = trim($value);
			}
			$value = wp_unslash($value);
		}
		update_site_option($option, $value);
	}

	audition_flush_rewrite_rules();

	if (! count(get_settings_errors())) {
		add_settings_error('general', 'settings_updated', __('Settings saved.'), 'updated');
	}
	set_transient('settings_errors', get_settings_errors(), 30);

	$goback = add_query_arg('settings-updated', 'true', wp_get_referer());
	wp_redirect($goback);
	exit;
}

/**
 * Add contextual help to settings pages.
 */
function audition_admin_add_settings_help_tabs($screen) {
	global $plugin_page;

	$help_tabs = $sidebar_links = array();

	if (! audition_admin()->has_page($plugin_page)) {
		return;
	}

	// Core page
	if ('audition' == $plugin_page) {
		$help_tabs['overview'] = array(
			'id'      => 'audition-overview',
			'title'   => __('Overview'),
			'content' => '<p>' . implode('</p><p>', array(
				__('Welcome to Audition!', 'audition'),
				__('Below, you can configure how you would like users to register and log in to your site.', 'audition'),
				__('Additionally, you can change the slugs that are used to generate the URLs that represent specific actions.', 'audition'),
				__('You must click the Save Changes button at the bottom of the screen for new settings to take effect.'),
			)) . '</p>',
		);

		$sidebar_links['documentation'] = array(
			'title' => __('View Documentation', 'audition'),
			'url'   => 'https://docs.thememylogin.com',
		);

		$sidebar_links['support'] = array(
			'title' => __('Get Support', 'audition'),
			'url'   => 'https://wordpress.org/support/plugin/audition',
		);
	}

	// Add the help tabs
	if (! empty($help_tabs)) {
		foreach ($help_tabs as $help_tab) {
			$screen->add_help_tab($help_tab);
		}
	}

	// Add the sidebar links
	if (! empty($sidebar_links)) {
		$sidebar_content = '<p><strong>' . __('For more information:') . '</strong></p>';
		foreach ($sidebar_links as $sidebar_link) {
			$sidebar_content .= sprintf('<p><a href="%s">%s</a></p>',
				$sidebar_link['url'],
				$sidebar_link['title']
			);
		}
		$screen->set_help_sidebar($sidebar_content);
	}
}
