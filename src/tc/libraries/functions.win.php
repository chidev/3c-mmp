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
	
	
	if( ! function_exists('checkdnsrr'))
	{
		/**
		 * Replaces unix command checkdnsrr.
		 *
		 * @param		string	ip-address/hostname
		 * @param		string	type
		 * @return	boolean
		 */
		function checkdnsrr($host, $type='mx')
		{
			// Use windows native commang to check dns record.
			$result = explode("\n",strstr(shell_exec('nslookup -type='.$type.' '.escapeshellarg($host).' 4.2.2.3'),"\n\n"));
				
			// Return results.		
			return ($result[2]) ? TRUE : FALSE;
		}
	}
	
	if ( ! function_exists('dns_check_record'))
	{
		/**
		 * checkdnsrr wrapper function.
		 */
		function dns_check_record($host, $type='mx')
		{
			return checkdnsrr($host, $type);
		}
	}
?>