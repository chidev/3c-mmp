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
	
	
	/**
	 * Generate a css id based on a string
	 *
	 * @param  string
	 * @return string
	 */
	function tc_css_id($str) {
		$str = preg_replace('/[^a-zA-Z0-9\s-_]/', '', $str);
		$str = strtolower(str_replace(array(' ','_'), '-', $str));
		return $str;
	}
	
	/**
	 * Generate a css class based on a string
	 *
	 * @param  string
	 * @return string
	 */
	function tc_css_class($str) {
		$str = preg_replace('/[^a-zA-Z0-9\s-_]/', '', $str);
		$str = strtolower(str_replace(array(' ','-'), '_', $str));
		return $classes;
	}
?>