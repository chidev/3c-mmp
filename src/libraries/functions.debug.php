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
	 * DEBUG HOOKS
	 */
	function list_hooked_functions($tag = false)
	{
		global	$wp_filter;
		if ($tag)
		{
			$hook[$tag]=$wp_filter[$tag];
			
			if ( ! is_array($hook[$tag]))
			{
				trigger_error("Nothing found for '$tag'	hook", E_USER_WARNING);
				return;
			}
	}
	else {
		$hook = $wp_filter;
		
		ksort($hook);
	}
	
		echo '<pre>';
		
		foreach($hook as	$tag =>	$priority)
		{
			echo "<br	/>&gt;&gt;&gt;&gt;&gt;\t<strong>$tag</strong><br />";
			
			ksort($priority);
			
			foreach($priority	as $priority =>	$function)
			{
				echo $priority;
				foreach($function	as $name =>	$properties) echo	"\t$name<br	/>";
			}
		}
		
		echo '</pre>';
	}
?>