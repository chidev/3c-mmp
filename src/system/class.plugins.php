<?php defined('TC_SYSTEM_PATH') OR die('No direct access allowed.<br/>'.__FILE__);
/**
 * 3rd Corner Studios Wordpress Framework
 *
 * @version			0.001
 * @package			TC_WP_001
 * @author			Jean-Patrick Smith
 * @copyright		2011 3rd Corner Studios
 * @url					http://www.3rdcornerstudios.com
 */


	class TC_Plugin
	{
		// Action path
		public static $_actions = array();

		// Plugins loaded
		public static $_loaded = array();

		public function __construct()
		{
		}



		// Parse incoming action:
		public static function parse_actions($input = 'post', $autofind = true)
		{
			global $tc_plugin_core;

			// We have no actions yet
			$action_paths = array();

			// If autofind is off the input must be found in a specific request type:
			if ( ! $autofind)
			{
				switch($input)
				{
					case 'get':
						$action_paths = $_GET['tc_actions'];
						break;
					case 'session':
						$action_paths = $_SESSION['tc_actions'];
						break;
					default:
						$action_paths = $_POST['tc_actions'];
						break;
				}
			}

			// If autofind is on, post then get then session have priority:
			if ($autofind && isset($_POST['tc_actions']))
			{
				$action_paths = $_POST['tc_actions'];
			}
			elseif ($autofind && isset($_GET['tc_actions']))
			{
				$action_paths = $_GET['tc_actions'];
			}
			elseif ($autofind && isset($_SESSION['tc_actions']))
			{
				$action_paths = $_SESSION['tc_actions'];
			}

			foreach ($action_paths as $action_path)
			{
				// Sanitize input
				$action_path = filter_var($action_path, FILTER_SANITIZE_STRING);

				// If we've found a path of action:
				if ($action_path !== false)
				{
					// Split actions into different parts
					$actions = explode('.', filter_var($action_path, FILTER_SANITIZE_STRING));

					// We must have at least namespace.controller.method
					if (count($actions) !== 4)
						return false;

					// Fill variables:
					$namespace = $actions[0];
					$class_name = $actions[1];
					$method = $actions[2];
					$id = $actions[3];

					$filename = $tc_plugin_core->get_filename($class_name);

					self::$_actions[$name] = array
					(
						'id'					=> $id,
						'filename'		=> $filename,
						'namespace'		=> $namespace,
						'class_name'	=> $class_name,
						'method'			=> $method
					);

					if ( ! class_exists($class_name))
					{
						include(TC_PLUGINS_PATH.'/'.$filename);
						// todo: self::load_plugin
						// todo: self::process_action
					}

					$varname = substr($class_name, 3);

					global $tc_plugins;

					call_user_func(array($tc_plugins[$varname][$id], $method), $id);
				}
			}
		}

		// Get name from class:
		public static function get_filename($class, $namespace = 'plugins')
		{
			$find = array('tc_', '_');
			$replace = array($namespace.'.', '.');
			return str_replace($find, $replace, $class).'.php';
		}

		// Generate nonce for current plugin
		public function get_nonce($class_name = 'tc_plugins')
		{
			return wp_create_nonce($class_name);
		}

		// Generate hidden fields for nonce/CSRF and actions
		public function get_hidden($id = null, $class_name = null, $action = 'submit')
		{
			($class_name === null) and $class_name = get_class($this);
			($id === null) and $id = mt_rand();
			echo '<input type="hidden" name="wp_nonce" value="'.$this->get_nonce($class_name).'" />';
			echo '<input type="hidden" name="tc_actions[]" value="'.$this->add_action($id, $class_name, $action).'" />';
		}

		// Add action to perform after next request (post/get/session) data
		public function add_action($id, $class, $action, $namespace = 'plugins')
		{
			$class_name = is_object($class)
								 ? get_class($class)
								 : $class;

			$action_path = array($namespace, $class_name, $action, $id);

			return implode('.', $action_path);
		}

		// Process actions
		public function process_actions()
		{

		}

		// Validate actions
		public function validate_action($action)
		{
			return $this->validate_nonce($action);
		}
		public function validate_nonce($action = null)
		{
			($action === null) and $action = get_class($this);
			return wp_verify_nonce($_POST['wp_nonce'], $action);
		}
	}
?>