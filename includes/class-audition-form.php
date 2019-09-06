<?php

/**
 * Audition Form Class
 *
 * @package Audition
 * @subpackage Forms
 */

/**
 * Class used to implement the form object.
 */
class Audition_Form {

	/**
	 * The form name.
	 */
	protected $name;

	/**
	 * The form action.
	 */
	protected $action;

	/**
	 * The form method.
	 */
	protected $method;

	/**
	 * The form fields.
	 */
	protected $fields = array();

	/**
	 * The form attributes.
	 */
	protected $attributes = array();

	/**
	 * The form errors.
	 */
	protected $errors;

	/**
	 * The form links.
	 */
	protected $links = array();

	/**
	 * The arguments used for rendering the field.
	 */
	public $render_args = array();

	/**
	 * Create a new instance.
	 */
	public function __construct($name, $args = array()) {

		$this->set_name($name);

		$args = wp_parse_args($args, array(
			'action'     => '',
			'method'     => 'post',
			'attributes' => array(),
		));

		$this->set_action($args['action']);
		$this->set_method($args['method']);

		$this->errors = new WP_Error;

		// Add the default links
		foreach (audition_get_actions() as $action) {
			if ($action->show_on_forms && $action->get_name() != $this->get_name()) {
				$this->add_link($action->get_name(), array(
					'text' => true === $action->show_on_forms ? $action->get_title() : $action->show_on_forms,
					'url'  => $action->get_url(),
				));
			}
		}

		foreach ((array) $args['attributes'] as $key => $value) {
			$this->add_attribute($key, $value);
		}

		if (! empty($args['render_args'])) {
			$this->render_args = $args['render_args'];
		}
	}

	/**
	 * Get the form name.
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Set the form name.
	 */
	protected function set_name($name) {
		$this->name = sanitize_key($name);
	}

	/**
	 * Get the form action.
	 */
	public function get_action() {
		return apply_filters('audition_get_form_action', $this->action, $this);
	}

	/**
	 * Set the form action.
	 */
	public function set_action($action) {
		$this->action = $action;
	}

	/**
	 * Get the form method.
	 */
	public function get_method() {
		return apply_filters('audition_get_form_method', $this->method, $this);
	}

	/**
	 * Set the form method.
	 */
	public function set_method($method) {
		$method = strtolower($method);
		if (! in_array($method, array('get', 'post'))) {
			$method = 'post';
		}
		$this->method = $method;
	}

	/**
	 * Add an attribute.
	 */
	public function add_attribute($key, $value = null) {
		$this->attributes[ $key ] = $value;
	}

	/**
	 * Remove an attribute.
	 */
	public function remove_attribute($key) {
		if (isset($this->attributes[ $key ])) {
			unset($this->attributes[ $key ]);
		}
	}

	/**
	 * Get an attribute.
	 */
	public function get_attribute($key) {
		if (isset($this->attributes[ $key ])) {
			return $this->attributes[ $key ];
		}
		return false;
	}

	/**
	 * Get all attributes.
	 */
	public function get_attributes() {
		return $this->attributes;
	}

	/**
	 * Add a field.
	 */
	public function add_field(Audition_Form_Field $field) {

		$this->fields[ $field->get_name() ] = $field;

		return $field;
	}

	/**
	 * Remove a field.
	 */
	public function remove_field($field) {
		if ($field instanceof Audition_Form_Field) {
			unset($this->fields[ $field->get_name() ]);
		} else {
			unset($this->fields[ $field ]);
		}
	}

	/**
	 * Get a field.
	 */
	public function get_field($field) {
		if (isset($this->fields[ $field ])) {
			return $this->fields[ $field ];
		}
		return false;
	}

	/**
	 * Get all fields.
	 */
	public function get_fields() {
		$priorities    = array();
		$sorted_fields = array();

		// Prioritize the fields
		foreach($this->fields as $field) {
			$priority = $field->get_priority();
			if (! isset($priorities[ $priority ])) {
				$priorities[ $priority ] = array();
			}
			$priorities[ $priority ][] = $field;
		}

		ksort($priorities);

		// Sort the fields
		foreach ($priorities as $priority => $fields) {
			foreach ($fields as $field) {
				$sorted_fields[] = $field;
			}
		}
		unset($priorities);

		return $sorted_fields;
	}

	/**
	 * Add an error.
	 */
	public function add_error($code, $message, $severity = 'error') {
		$this->errors->add($code, $message, $severity);
	}

	/**
	 * Get the errors.
	 */
	public function get_errors() {
		return $this->errors;
	}

	/**
	 * Set the errors.
	 */
	public function set_errors(WP_Error $errors) {
		$this->errors = $errors;
	}

	/**
	 * Determine if the form has errors.
	 */
	public function has_errors() {
		return (bool) $this->errors->get_error_code();
	}

	/**
	 * Render the errors.
	 */
	public function render_errors() {

		if (! $this->has_errors()) {
			return;
		}

		$errors   = array();
		$messages = array();

		foreach ($this->errors->get_error_codes() as $code) {

			$severity = $this->errors->get_error_data($code);

			foreach ($this->errors->get_error_messages($code) as $error) {
				if ('message' == $severity) {
					$messages[] = $error;
				} else {
					$errors[] = $error;
				}
			}
		}

		$output = '';

		if (! empty($errors)) {
			$output .= sprintf('<ul class="audition-errors"><li class="audition-error">%s</li></ul>',
				apply_filters('login_errors', implode("</li>\n<li class=\"audition-error\">", $errors))
			);
		}

		if (! empty($messages)) {
			$output .= sprintf('<ul class="audition-messages"><li class="audition-message">%s</li></ul>',
				apply_filters('login_messages', implode("</li>\n<li class=\"audition-message\">", $messages))
			);
		}

		return $output;
	}

	/**
	 * Add a link.
	 */
	public function add_link($link, $args = array()) {
		$args = wp_parse_args($args, array(
			'text' => '',
			'url'  => '',
		));

		$link = sanitize_key($link);

		$this->links[ $link ] = $args;
	}

	/**
	 * Remove a link.
	 */
	public function remove_link($link) {
		unset($this->links[ $link ]);
	}

	/**
	 * Get a link.
	 */
	public function get_link($link) {
		if (isset($this->links[ $link ])) {
			return $this->links[ $link ];
		}
		return false;
	}

	/**
	 * Get all links.
	 */
	public function get_links() {
		/**
		 * Filter the form links.
		 */
		return apply_filters('audition_get_form_links', $this->links, $this);
	}

	/**
	 * Render the links.
	 */
	public function render_links() {

		if (! $links = $this->get_links()) {
			return;
		}

		$output = '<ul class="audition-links">';

		foreach ($links as $name => $link) {
			$output .= sprintf('<li class="audition-%s-link"><a href="%s">%s</a></li>',
				esc_attr($name),
				esc_url($link['url']),
				esc_html($link['text'])
			);
		}

		$output .= '</ul>';

		return $output;
	}

	/**
	 * Render the form.
	 */
	public function render($args = array()) {
		$defaults = wp_parse_args($this->render_args, array(
			'container'       => 'div',
			'container_class' => 'tml audition-%s',
			'container_id'    => '',
			'before'          => '',
			'after'           => '',
			'show_links'      => true,
		));

		/**
		 * Fires before rendering a form.
		 */
		do_action('audition_render_form', $this->name, $this);

		$args = wp_parse_args($args, $defaults);

		$output = $args['before'];

		if (! empty($args['container'])) {
			$output .= '<' . $args['container'];
			if (! empty($args['container_id'])) {
				$output .= ' id="' . esc_attr(sprintf($args['container_id'], $this->name)) . '"';
			}
			if (! empty($args['container_class'])) {
				$output .= ' class="' . esc_attr(sprintf($args['container_class'], $this->name)) . '"';
			}
			$output .= ">\n";
		}

		/**
		 * Filter the content before the form.
		 */
		$output = apply_filters('audition_before_form', $output, $this->name, $this);

		$output .= $this->render_errors();

		$output .= '<form name="' . esc_attr($this->get_name()) . '" action="' . esc_url($this->get_action()) . '" method="' . esc_attr($this->get_method()) . '"';
		foreach ($this->get_attributes() as $key => $value) {
			$output .= ' ' . $key . '="' . esc_attr($value) . '"';
		}
		$output .= ">\n";

		foreach ($this->get_fields() as $field) {
			$output .= $field->render() . "\n";
		}

		$output .= "</form>\n";

		/**
		 * Filter the content after the form.
		 */
		$output = apply_filters('audition_after_form', $output, $this->name, $this);

		if ($args['show_links']) {
			$output .= $this->render_links();
		}

		if (! empty($args['container'])) {
			$output .= '</' . $args['container'] . ">\n";
		}

		$output .= $args['after'];

		return $output;
	}
}
