<?php

/**
 * Audition Plugin Class
 */

/**
 * Class used to implement the plugin object.
 */
final class Audition {

	private static $instance;

	protected $actions = array();

	protected $forms = array();

	private $data = array();

	public static function get_instance() {
		if (null === self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function register_action(Audition_Action $action) {

		$this->actions[ $action->get_name() ] = $action;

		$action->add_callback_hook();

		/**
		 * Fires after registering an action.
		 *
		 * @since 7.0
		 *
		 * @param string                $name   The action name.
		 * @param Audition_Action $action The action object.
		 */
		do_action('audition_registered_action', $action->get_name(), $action);

		return $action;
	}

	/**
	 * Unregister an action.
	 *
	 * @since 7.0
	 *
	 * @param string|Audition_Action $action The action name or object.
	 */
	public function unregister_action($action) {
		if ($action instanceof Audition_Action) {

			$action->remove_callback_hook();

			unset($this->actions[ $action->get_name() ]);
		} else {
			if ($action = $this->get_action($action)) {

				$action->remove_callback_hook();

				unset($this->actions[ $action->get_name() ]);
			}
		}
	}

	/**
	 * Get an action.
	 *
	 * @since 7.0
	 *
	 * @param string $action The action name.
	 * @return Audition_Action|bool The action object if it exists or false otherwise.
	 */
	public function get_action($action) {
		if (isset($this->actions[ $action ])) {
			return $this->actions[ $action ];
		}
		return false;
	}

	/**
	 * Get all actions.
	 *
	 * @since 7.0
	 *
	 * @return array The actions.
	 */
	public function get_actions() {
		return $this->actions;
	}

	/**
	 * Register a form.
	 *
	 * @since 7.0
	 *
	 * @param Audition_Form $form The form object.
	 * @return Audition_Form The form object.
	 */
	public function register_form(Audition_Form $form) {

		$this->forms[ $form->get_name() ] = $form;

		/**
		 * Fires after registering a form.
		 *
		 * @since 7.0
		 *
		 * @param string              $name The form name.
		 * @param Audition_Form $form The form object.
		 */
		do_action('audition_registered_form', $form->get_name(), $form);

		return $form;
	}

	/**
	 * Unregister a form.
	 *
	 * @since 7.0
	 *
	 * @param string|Audition_Form $form The form name or object.
	 */
	public function unregister_form($form) {
		if ($form instanceof Audition_Form) {
			unset($this->forms[ $form->get_name() ]);
		} else {
			unset($this->forms[ $form ]);
		}
	}

	/**
	 * Get a form.
	 *
	 * @since 7.0
	 *
	 * @param string $form The form name.
	 * @return Audition_Form|bool The form object if it exists or false otherwise.
	 */
	public function get_form($form) {
		if (isset($this->forms[ $form ])) {
			return $this->forms[ $form ];
		}
		return false;
	}

	/**
	 * Get all forms.
	 *
	 * @since 7.0
	 *
	 * @return array The forms.
	 */
	public function get_forms() {
		return $this->forms;
	}

	/**
	 * Register an extension.
	 *
	 * @since 7.0
	 *
	 * @param Audition_Extension $extension The extension object.
	 * @return Audition_Extension The extension object.
	 */
	public function register_extension(Audition_Extension $extension) {

		$this->extensions[ $extension->get_name() ] = $extension;

		/**
		 * Fires after registering an extension.
		 *
		 * @since 7.0
		 *
		 * @param string                   $name   The extension name.
		 * @param Audition_Extension $extension The extension object.
		 */
		do_action('audition_registered_extension', $extension->get_name(), $extension);

		return $extension;
	}

	/**
	 * Unregister an extension.
	 *
	 * @since 7.0
	 *
	 * @param string|Audition_Extension $extension The extension name or object.
	 */
	public function unregister_extension($extension) {
		if ($extension instanceof Audition_Extension) {
			unset($this->extensions[ $extension->get_name() ]);
		} else {
			unset($this->extensions[ $extension ]);
		}
	}

	/**
	 * Get an extension.
	 *
	 * @since 7.0
	 *
	 * @param string $extension The extension name.
	 * @return Audition_Extension|bool The extension object if it exists or false otherwise.
	 */
	public function get_extension($extension) {
		if (isset($this->extensions[ $extension ])) {
			return $this->extensions[ $extension ];
		}
		return false;
	}

	/**
	 * Get all extensions.
	 *
	 * @since 7.0
	 *
	 * @return array The extensions.
	 */
	public function get_extensions() {
		return $this->extensions;
	}

	/**
	 * Fire an action hook when the plugin is activated.
	 *
	 * @since 7.0
	 */
	public function activate() {
		/**
		 * Fires when the plugin is being activated.
		 *
		 * @since 7.0
		 */
		do_action('audition_activate');
	}

	/**
	 * Fire an action hook when the plugin is deactivated.
	 *
	 * @since 7.0
	 */
	public function deactivate() {
		/**
		 * Fires when the plugin is being deactivated.
		 *
		 * @since 7.0
		 */
		do_action('audition_deactivate');
	}

	/**
	 * Get arbitrary data.
	 *
	 * @since 7.0
	 *
	 * @param string $name The property name.
	 * @param mixed  $default The value to return if the property is not set.
	 * @return mixed The property value or $default if not set.
	 */
	public function get_data($name, $default = false) {
		if (array_key_exists($name, $this->data)) {
			return $this->data[ $name ];
		}
		return $default;
	}

	/**
	 * Set arbitrary data.
	 *
	 * @since 7.0
	 *
	 * @param string|array $name  The property name or an array of properties.
	 * @param mixed        $value The property value.
	 */
	public function set_data($name, $value = '') {
		if (is_array($name)) {
			foreach($name as $k => $v) {
				$this->data[ $k ] = $v;
			}
		} else {
			$this->data[ $name ] = $value;
		}
	}

	/**
	 * Construct the instance.
	 *
	 * @since 7.0
	 */
	protected function __construct() {
		/**
		 * Fires when TML has been initialized.
		 *
		 * @since 7.0
		 *
		 * @param Audition $tml The TML object.
		 */
		do_action('audition_init', $this);

		// Get the main plugin file path
		$plugin_file = str_replace(array('src', 'build'), '', AUDITION_PATH . 'audition.php');

		// Run the activation hook
		register_activation_hook($plugin_file, array($this, 'activate'));

		// Run the deactivation hook
		register_deactivation_hook($plugin_file, array($this, 'deactivate'));
	}

	/**
	 * Don't allow cloning.
	 *
	 * @since 7.0
	 */
	private function __clone() {}

	/**
	 * Don't allow unserialization.
	 *
	 * @since 7.0
	 */
	private function __wakeup() {}

	/**
	 * Handle some deprecated methods that other plugins use.
	 *
	 * @since 7.0.1
	 */
	public static function __callStatic($name, $args) {
		switch ($name) {
			case 'get_object' :
				return self::get_instance();
				break;

			case 'is_audition_page' :
				return audition_is_action(isset($args[0]) ? $args[0] : '');
				break;
		}
	}
}
