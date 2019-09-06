<?php

/**
 * Audition Form Field Class
 */

/**
 * Class used to implement the form field object.
 */
class Audition_Form_Field {

	/**
	 * The field name.
	 */
	protected $name;

	/**
	 * The field type.
	 */
	protected $type;

	/**
	 * The field value.
	 */
	protected $value;

	/**
	 * The field label.
	 */
	protected $label;

	/**
	 * The field description.
	 */
	protected $description;

	/**
	 * The field error.
	 */
	protected $error;

	/**
	 * The field content.
	 */
	protected $content;

	/**
	 * The field options.
	 */
	protected $options = array();

	/**
	 * The field attributes.
	 */
	protected $attributes = array();

	/**
	 * The field classes.
	 */
	protected $classes = array();

	/**
	 * The field's parent form.
	 */
	protected $form;

	/**
	 * The field's priority within the form.
	 */
	protected $priority = 10;

	/**
	 * The arguments used for rendering the field.
	 */
	public $render_args = array();

	/**
	 * Create a new instance.
	 */
	public function __construct(Audition_Form $form, $name, $args = array()) {
		$this->set_form($form);
		$this->set_name($name);

		$args = wp_parse_args($args, array(
			'type'        => 'text',
			'value'       => '',
			'label'       => '',
			'description' => '',
			'error'       => '',
			'content'     => '',
			'options'     => array(),
			'attributes'  => array(),
		));

		$this->set_type($args['type']);
		$this->set_value($args['value']);
		$this->set_label($args['label']);
		$this->set_description($args['description']);
		$this->set_error($args['error']);
		$this->set_content($args['content']);
		$this->set_options($args['options']);

		if (! empty($args['id'])) {
			$this->add_attribute('id', $args['id']);
		}

		if (! empty($args['class'])) {
			$this->add_class($args['class']);
		} elseif ('hidden' != $this->get_type()) {
			if (in_array($args['type'], array('button', 'submit', 'reset'))) {
				$class = 'audition-button';
			} elseif (in_array($args['type'], array('checkbox', 'radio', 'radio-group'))) {
				$class = 'audition-checkbox';
			} else {
				$class = 'audition-field';
			}
			$this->add_class($class);
		}

		if ('checkbox' == $args['type'] && ! empty($args['checked'])) {
			$this->add_attribute('checked', 'checked');
		}

		foreach ((array) $args['attributes'] as $key => $value) {
			$this->add_attribute($key, $value);
		}

		if (isset($args['priority'])) {
			$this->set_priority($args['priority']);
		}

		if (! empty($args['render_args'])) {
			$this->render_args = $args['render_args'];
		}
	}

	/**
	 * Get the field name.
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Set the field name.
	 */
	protected function set_name($name) {
		$this->name = sanitize_key($name);
	}

	/**
	 * Get the field type.
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Set the field type.
	 */
	public function set_type($type) {
		if (empty($type)) {
			$type = 'text';
		}
		$this->type = $type;
	}

	/**
	 * Get the field value.
	 */
	public function get_value() {
		return $this->value;
	}

	/**
	 * Set the field value.
	 */
	public function set_value($value) {
		$this->value = $value;
	}

	/**
	 * Get the field label.
	 */
	public function get_label() {
		return apply_filters('audition_get_form_field_label', $this->label, $this);
	}

	/**
	 * Set the field label.
	 */
	public function set_label($label) {
		$this->label = $label;
	}

	/**
	 * Get the field description.
	 */
	public function get_description() {
		return apply_filters('audition_get_form_field_description', $this->description, $this);
	}

	/**
	 * Set the field description.
	 */
	public function set_description($description) {
		$this->description = $description;
	}

	/**
	 * Get the field error message.
	 */
	public function get_error() {
		return $this->error;
	}

	/**
	 * Set the field error message.
	 */
	public function set_error($error = '') {
		$this->error = $error;
	}

	/**
	 * Get the field content.
	 */
	public function get_content() {
		if (is_callable($this->content)) {
			$content = call_user_func_array($this->content, array($this));
		} else {
			$content = $this->content;
		}

		return apply_filters('audition_get_form_field_content', $content, $this);
	}

	/**
	 * Set the field content.
	 */
	public function set_content($content = '') {
		$this->content = $content;
	}

	/**
	 * Get the field options.
	 */
	public function get_options() {
		return apply_filters('audition_get_form_field_options', $this->options, $this);
	}

	/**
	 * Set the field options.
	 */
	public function set_options($options = array()) {
		$this->options = (array) $options;
	}

	/**
	 * Get the field's parent form.
	 */
	public function get_form() {
		return $this->form;
	}

	/**
	 * Set the field's parent form.
	 */
	public function set_form(Audition_Form $form) {
		$this->form = $form;
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
	 * Add a class.
	 */
	public function add_class($class) {
		if (! is_array($class)) {
			$class = explode(' ', $class);
		}
		$this->classes = array_unique(array_merge($this->classes, $class));
	}

	/**
	 * Remove a class.
	 */
	public function remove_class($class) {
		$classes = array_flip($this->classes);
		if (isset($classes[ $class ])) {
			unset($classes[ $class ]);
			$this->classes = array_keys($classes);
		}
	}

	/**
	 * Determine if the field has a given class.
	 */
	public function has_class($class) {
		return in_array($class, $this->classes);
	}

	/**
	 * Get all classes.
	 */
	public function get_classes() {
		return $this->classes;
	}

	/**
	 * Set the priority.
	 */
	public function set_priority($priority) {
		$this->priority = (int) $priority;
	}

	/**
	 * Get the priority.
	 */
	public function get_priority() {
		return $this->priority;
	}

	/**
	 * Render the field.
	 */
	public function render($args = array()) {
		$is_hidden = ('hidden' == $this->get_type());

		if ('action' == $this->get_type()) {
			return audition_buffer_action_hook($this->get_name(), $this->render_args);
		}

		$defaults = wp_parse_args($this->render_args, array(
			'before'         => $is_hidden ? '' : '<div class="audition-field-wrap audition-%s-wrap">',
			'after'          => $is_hidden ? '' : '</div>',
			'control_before' => '',
			'control_after'  => '',
		));

		do_action('audition_render_form_field', $this->form->get_name(), $this->name, $this);

		$args = wp_parse_args($args, $defaults);

		$output = '';

		if (! empty($args['before'])) {
			$output .= sprintf($args['before'], $this->get_name()) . "\n";
		}

		$output = apply_filters('audition_before_form_field', $output, $this->form->get_name(), $this->name, $this);

		$attributes = '';
		foreach ($this->get_attributes() as $key => $value) {
			$attributes .= ' ' . $key . '="' . esc_attr($value) . '"';
		}
		if ($classes = $this->get_classes()) {
			$attributes .= ' class="' . implode(' ', $classes) . '"';
		}

		$label = '';
		if ($this->get_label()) {
			if ($this->get_attribute('id')) {
				$label = sprintf(
					'<label class="audition-label" for="%1$s">%2$s</label>',
					$this->get_attribute('id'),
					$this->get_label()
				) . "\n";
			} else {
				$label = sprintf(
					'<span class="audition-label">%s</span>',
					$this->get_label()
				) . "\n";
			}
		}

		$error = '';
		if ($this->get_error()) {
			$error = '<span class="audition-error">' . $this->get_error() . '</span>';
		}

		switch ($this->get_type()) {
			case 'custom' :
				$output .= $label;
				$output .= $this->get_content();
				break;

			case 'checkbox' :
				$output .= $args['control_before'];
				$output .= '<input name="' . $this->get_name() . '" type="checkbox" value="' . esc_attr($this->get_value()) . '"' . $attributes . ">\n";
				$output .= $args['control_after'];
				$output .= $label;
				break;

			case 'radio-group' :
				$output .= $label;
				$output .= $error;
				$output .= $args['control_before'];

				$options = array();
				foreach ($this->get_options() as $value => $label) {
					$id = $this->get_name() . '_' . $value;

					$option = '<input name="' . $this->get_name() . '" id="' . $id . '" type="radio" value="' . esc_attr($value) . '"' . $attributes;
					if ($this->get_value() == $value) {
						$option .= ' checked="checked"';
					}
					$option .= '>' . "\n";
					$option .= '<label class="audition-label" for="' . $id . '">' . esc_html($label) . "</label>\n";

					$options[] = $option;
				}
				$output .= implode('<br />', $options);

				$output .= $args['control_after'];
				break;

			case 'dropdown' :
				$output .= $label;
				$output .= $error;
				$output .= $args['control_before'];
				$output .= '<select name="' . $this->get_name() . '"' . $attributes . ">\n";
				foreach ($this->get_options() as $value => $option) {
					$output .= '<option value="' . esc_attr($value) . '"';
					if ($this->get_value() == $value) {
						$output .= ' selected="selected"';
					}
					$output .= '>' . esc_html($option) . "</option>\n";
				}
				$output .= "</select>\n";
				$output .= $args['control_after'];
				break;

			case 'textarea' :
				$output .= $label;
				$output .= $args['control_before'];
				$output .= '<textarea name="' . $this->get_name() . '"' . $attributes . '>' . $this->get_value() . "</textarea>\n";
				$output .= $args['control_after'];
				break;

			default :
				$output .= $label;
				$output .= $error;
				$output .= $args['control_before'];
				$output .= '<input name="' . $this->get_name() . '" type="' . $this->get_type() . '" value="' . esc_attr($this->get_value()) . '"' . $attributes . ">\n";
				$output .= $args['control_after'];
		}

		if ($this->get_description()) {
			$output .= '<span class="audition-description">' . $this->get_description() . "</span>\n";
		}

		$output = apply_filters('audition_after_form_field', $output, $this->form->get_name(), $this->name, $this);

		if (! empty($args['after'])) {
			$output .= $args['after'] . "\n";
		}

		return $output;
	}
}
