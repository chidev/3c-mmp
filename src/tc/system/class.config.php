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
	

class TC_Config
{
	public $conf;
	
	public function __construct()
	{
		foreach (glob(TC_CONFIG_PATH.'/conf.*.php') as $cnf)
		{
			$key = str_replace(array('conf.','.php'), '', basename($cnf));
			
			$status = true;
			
			if (stripos($key, '.') !== FALSE)
			{
				list($group, $driver) = explode('.', $key);
				
				$status = ($this->conf[$group]['driver'] == $driver);
			}
			
			if ($status)
			{
				$config = array();
				include($cnf);
				$this->conf[$key] = $config;
			}
		}
	}
	
	public function save()
	{
		//file_put_contents(TC_CONFIG_PATH, serialize($this->conf))
	}
	
	public function get($key)
	{
		if (strpos($key, '.') !== FALSE)
		{
			list($group, $key) = explode('.', $key);
			$val = isset($this->conf[$group][$key]) ? $this->conf[$group][$key] : false;
		}
		else {
			$val = isset($this->conf[$key]) ? $this->conf[$key] : false;
		}
		return $val;
	}
}
?>
