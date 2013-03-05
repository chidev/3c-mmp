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
	
	
	if ( ! function_exists('tc_validate_email'))
	{
		function tc_validate_email($email, $check_domain = false)
		{
			$status = filter_var(filter_var($email, FILTER_SANITIZE_EMAIL), FILTER_VALIDATE_EMAIL);
			
			if ($check_domain)
			{
				$host = explode('@', $email, 2);
				$status = (isset($host[1]) && dns_check_record($host[1]));
			}
			
			return $status;
		}
	}
	
?>