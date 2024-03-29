<?php

/**
 * Audition Form Functions
 *
 * @package Audition
 * @subpackage Forms
 */

/**
 * Register the default forms.
 *
 * @since 7.0
 */
function audition_register_default_forms() {
	if (is_admin()) {
		return;
	}

	audition_register_login_form();
	audition_register_registration_form();
	audition_register_lost_password_form();
	audition_register_password_reset_form();
}

/**
 * Register the login form.
 *
 * @since 7.0
 */
function audition_register_login_form() {

	audition_register_form('login', array(
		'action' => audition_get_action_url('login'),
	));

	audition_add_form_field('login', 'log', array(
		'type'     => 'text',
		'label'    => audition_get_username_label('login'),
		'value'    => audition_get_request_value('log', 'post'),
		'id'       => 'user_login',
		'priority' => 10,
	));

	audition_add_form_field('login', 'pwd', array(
		'type'     => 'password',
		'label'    => __('Password'),
		'value'    => '',
		'id'       => 'user_pass',
		'priority' => 15,
	));

	audition_add_form_field('login', 'login_form', array(
		'type'     => 'action',
		'priority' => 20,
	));

	audition_add_form_field('login', 'rememberme', array(
		'type'     => 'checkbox',
		'label'    => __('Remember Me'),
		'value'    => 'forever',
		'id'       => 'rememberme',
		'priority' => 25,
	));

	audition_add_form_field('login', 'submit', array(
		'type'     => 'submit',
		'value'    => __('Log In'),
		'priority' => 30,
	));

	$redirect_to = audition_get_request_value('redirect_to');

	audition_add_form_field('login', 'redirect_to', array(
		'type'     => 'hidden',
		'value'    => apply_filters('login_redirect',
			! empty($redirect_to) ? $redirect_to : admin_url(), $redirect_to, null
		),
		'priority' => 30,
	));
}

/**
 * Register the registration form.
 *
 * @since 7.0
 */
function audition_register_registration_form() {

	audition_register_form('register', array(
		'action' => audition_get_action_url('register'),
	));

	if (audition_is_default_registration_type()) {
		audition_add_form_field('register', 'user_login', array(
			'type'     => 'text',
			'label'    => __('Username'),
			'value'    => audition_get_request_value('user_login', 'post'),
			'id'       => 'user_login',
			'priority' => 10,
		));
	} else {
		audition_add_form_field('register', 'user_login', array(
			'type'     => 'hidden',
			'label'    => '',
			'value'    => 'user' . md5(microtime()),
			'id'       => 'user_login',
			'priority' => 10,
		));
	}

	audition_add_form_field('register', 'user_email', array(
		'type'     => 'email',
		'label'    => __('Email'),
		'value'    => audition_get_request_value('user_email', 'post'),
		'id'       => 'user_email',
		'priority' => 15,
	));

	if (audition_allow_user_passwords()) {
		audition_add_form_field('register', 'user_pass1', array(
			'type'       => 'password',
			'label'      => __('Password'),
			'id'         => 'pass1',
			'attributes' => array(
				'autocomplete' => 'off',
			),
			'priority'   => 20,
		));

		audition_add_form_field('register', 'user_pass2', array(
			'type'       => 'password',
			'label'      => __('Confirm Password', 'audition'),
			'id'         => 'pass2',
			'attributes' => array(
				'autocomplete' => 'off',
			),
			'priority'   => 20,
		));

		audition_add_form_field('register', 'indicator', array(
			'type'     => 'custom',
			'content'  => '<div id="pass-strength-result" class="hide-if-no-js" aria-live="polite">' . __('Strength indicator') . '</div>',
			'priority' => 20,
		));

		audition_add_form_field('register', 'indicator_hint', array(
			'type'     => 'custom',
			'content'  => '<p class="description indicator-hint">' . wp_get_password_hint() . '</p>',
			'priority' => 20,
		));
	}

	audition_add_form_field('register', 'register_form', array(
		'type'     => 'action',
		'priority' => 25,
	));

	if (! audition_allow_user_passwords()) {
		audition_add_form_field('register', 'reg_passmail', array(
			'type'     => 'custom',
			'content'  => '<p id="reg_passmail">' . __('Registration confirmation will be emailed to you.') . '</p>',
			'priority' => 30,
		));
	}

	audition_add_form_field('register', 'submit', array(
		'type'     => 'submit',
		'value'    => __('Register'),
		'priority' => 35,
	));

	audition_add_form_field('register', 'redirect_to', array(
		'type'     => 'hidden',
		'value'    => apply_filters('registration_redirect', audition_get_request_value('redirect_to')),
		'priority' => 35,
	));
}

/**
 * Register the lost password form.
 *
 * @since 7.0
 */
function audition_register_lost_password_form() {

	audition_register_form('lostpassword', array(
		'action' => audition_get_action_url('lostpassword'),
	));

	audition_add_form_field('lostpassword', 'user_login', array(
		'type'     => 'text',
		'label'    => audition_get_username_label('lostpassword'),
		'value'    => '',
		'id'       => 'user_login',
		'priority' => 10,
	));

	audition_add_form_field('lostpassword', 'lostpassword_form', array(
		'type'     => 'action',
		'priority' => 15,
	));

	audition_add_form_field('lostpassword', 'submit', array(
		'type'     => 'submit',
		'value'    => __('Get New Password'),
		'priority' => 20,
	));

	audition_add_form_field('lostpassword', 'redirect_to', array(
		'type'     => 'hidden',
		'value'    => apply_filters('lostpassword_redirect', audition_get_request_value('redirect_to')),
		'priority' => 20,
	));
}

/**
 * Register the password reset form.
 *
 * @since 7.0
 */
function audition_register_password_reset_form() {

	audition_register_form('resetpass', array(
		'action'      => audition_get_action_url('resetpass'),
		'render_args' => array(
			'show_links' => false,
		),
	));

	audition_add_form_field('resetpass', 'pass1', array(
		'type'     => 'password',
		'label'    => __('New password'),
		'id'       => 'pass1',
		'priority' => 10,
	));

	audition_add_form_field('resetpass', 'pass2', array(
		'type'     => 'password',
		'label'    => __('Confirm new password'),
		'id'       => 'pass2',
		'priority' => 10,
	));

	audition_add_form_field('resetpass', 'indicator', array(
		'type'     => 'custom',
		'content'  => '<div id="pass-strength-result" class="hide-if-no-js" aria-live="polite">' . __('Strength indicator') . '</div>',
		'priority' => 10,
	));

	audition_add_form_field('resetpass', 'indicator_hint', array(
		'type'     => 'custom',
		'content'  => '<p class="description indicator-hint">' . wp_get_password_hint() . '</p>',
		'priority' => 10,
	));

	audition_add_form_field('resetpass', 'resetpass_form', array(
		'type'        => 'action',
		'priority'    => 15,
		'render_args' => array(wp_get_current_user())
	));

	audition_add_form_field('resetpass', 'submit', array(
		'type'     => 'submit',
		'value'    => __('Reset Password'),
		'priority' => 20,
	));

	$rp_cookie = 'wp-resetpass-' . COOKIEHASH;
	if (isset($_COOKIE[ $rp_cookie ]) && 0 < strpos($_COOKIE[ $rp_cookie ], ':')) {
		list($rp_login, $rp_key) = explode(':', wp_unslash($_COOKIE[ $rp_cookie ]), 2);

		audition_add_form_field('resetpass', 'rp_key', array(
			'type'     => 'hidden',
			'value'    => $rp_key,
			'priority' => 20,
		));
	}
}

/**
 * Register a form.
 *
 * @since 7.0
 *
 * @param string|Audition_Form $form The form name or object.
 * @param array                      $args {
 *     Optional. An array of arguments for registering a form.
 *
 *     @type bool $show_links Whether to show links to other actions or not.
 * }
 * @return Audition_Form The form object.
 */
function audition_register_form($form, $args = array()) {

	if (! $form instanceof Audition_Form) {
		$form = new Audition_Form($form, $args);
	}

	return audition()->register_form($form);
}

/**
 * Unregister a form.
 *
 * @since 7.0
 *
 * @param string|Audition_Form $form The form name or object.
 */
function audition_unregister_form($form) {
	audition()->unregister_form($form);
}

/**
 * Get a form.
 *
 * @since 7.0
 *
 * @param string|Audition_Form $form Optional. The form name or object.
 * @return Audition_Form|bool The form object or false if it doesn't exist.
 */
function audition_get_form($form = '') {

	if ($form instanceof Audition_Form) {
		return $form;
	}

	if (empty($form)) {
		if ($action = audition_get_action()) {
			$form = $action->get_name();
		}
	}

	return audition()->get_form($form);
}

/**
 * Get all forms.
 *
 * @since 7.0
 *
 * @return array The forms.
 */
function audition_get_forms() {
	return audition()->get_forms();
}

/**
 * Determine if a form exists.
 *
 * @since 7.0
 *
 * @param string $form The form name.
 * @return bool True if the form exists or false otherwise.
 */
function audition_form_exists($form) {
	return apply_filters('audition_form_exists', array_key_exists($form, audition_get_forms()));
}

/**
 * Add a form field.
 *
 * @since 7.0
 *
 * @param string|Audition_Form       $form  The form name or object.
 * @param string|Audition_Form_Field $field The field name or object.
 * @param array {
 *     Optional. An array of arguments for registering a form field.
 * }
 * @return Audition_Form_Field The field object.
 */
function audition_add_form_field($form, $field, $args = array()) {

	if (! $form = audition_get_form($form)) {
		return;
	}

	if (! $field instanceof Audition_Form_Field) {
		$field = new Audition_Form_Field($form, $field, $args);
	}

	return $form->add_field($field);
}

/**
 * Remove a form field.
 *
 * @since 7.0
 *
 * @param string|Audition_Form $form The form name or object.
 * @param string|Audition_Form_Field $field The field name or object.
 */
function audition_remove_form_field($form, $field) {

	if (! $form = audition_get_form($form)) {
		return;
	}

	$form->remove_field($field);
}

/**
 * Get a form field.
 *
 * @since 7.0
 *
 * @param string|Audition_Form $form  The form name or object.
 * @param string                     $field The field name.
 * @return Audition_Form_Field|bool The field object false if it doesn't exist.
 */
function audition_get_form_field($form, $field) {

	if (! $form = audition_get_form($form)) {
		return false;
	}

	return $form->get_field($field);
}

/**
 * Get all form fields.
 *
 * @since 7.0
 *
 * @param string|Audition_Form $form The form name or object.
 * @return array The form fields or false if the form doesn't exist.
 */
function audition_get_form_fields($form) {

	if (! $form = audition_get_form($form)) {
		return false;
	}

	return $form->get_fields();
}
