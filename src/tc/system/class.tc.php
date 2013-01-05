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

final class TC
{
	/**
	 * @var config object
	 */
	public static $config;
	
	/**
	 * @var cache object
	 */
	public static $cache;
	
	/**
	 * @var loaded plugins array
	 */
	public static $plugins;
	
	/**
	 * 3rd Corner Studios core controller constructor
	 */
	public static function init()
	{
		static $run;

		// This function can only be run once
		if ($run === TRUE)
			return;
		
		// Load configuration object
		TC::$config = new TC_Config();
		
		// Load cache
		TC::$cache = new TC_Cache(TC::$config->get('tc.config_driver'));
	}
	
	/**
	 * Load site specific logic
	 */
	public static function load_site()
	{
		TC::load_plugins();
		
		include(TC_APPLICATION_PATH.'/init.site.php');
	}
	
	/**
	 * Load all 3rd Corner Studios plugins
	 */
	private static function load_plugins()
	{
		static $loaded;
		
		if ($loaded === NULL)
		{
			global $doc;
			
			$key = $doc['page_id'];
			
			$plugins = TC::config('tc.plugins');
			
			$plugin_files = $plugins['global'];
			
			if (isset($plugins[$key]) && is_array($plugins[$key]))
			{
				$plugin_files = array_merge($plugin_files, $plugins[$key]);
			}
			
			//echo '<div style="padding:20px; background:#efefef;font-size:16px;color:#333;text-align:left;"><pre>';
			
			foreach ($plugin_files as $plugin)
			{
				$plugin = TC_PLUGINS_PATH.'/plugins.'.$plugin.'.php';
				$plugin_basename = basename($plugin);
				
				if (file_exists($plugin))
				{
					include($plugin);
					
					$cleanup = array('plugins.', '.php', '.');
					$cleaned = array('', '', '_');
					
					$plugin_name = ucwords(strtolower(str_replace($cleanup, $cleaned, $plugin_basename)));
					$plugin_class_name = 'TC_'.$plugin_name;
					
					global ${$plugin_name};
					
					try {
						//$plugin_class = new ReflectionClass($plugin_class_name);
						
						// autoload? // ${$plugin_name} = $plugin_class->newInstance();
						TC::$plugins[$plugin_name] = 1;
					}
					catch(Exception $e)
					{
						/* ... */
					}
					
					//echo '<div style="padding:20px; background:#efefef;font-size:16px;color:#333;text-align:left;"><pre>';
					//var_dump($plugin_name);
					//var_dump($plugin_class);
					//var_dump($contact_form);
					//echo '</pre></div>';
					
					//echo "\n";
				}
				else {
					//echo "\nskipped:\n";
					//var_dump(basename($plugin));
					//echo "\n";
				}
			}
			
			//echo '</pre></div>';
			
			$loaded = true;
		}
	}
	
	/**
	 * Load a config item
	 */
	public static function config($key)
	{
		return (TC::$config !== NULL) ? TC::$config->get($key) : false;
	}
	
	public static function cache_set($key, $data, $lifetime = false)
	{
		return (TC::$cache !== NULL) ? TC::$cache->set($key, $data, $lifetime) : false;
	}
	
	public static Function cache_get($key)
	{
		return (TC::$cache !== NULL) ? TC::$cache->get($key) : false;
	}
	
	public static Function cache_delete($key)
	{
		return (TC::$cache !== NULL) ? TC::$cache->delete($key) : false;
	}
	
}
?>