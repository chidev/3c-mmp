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

	include(TC_GLOBALS);

	// Setup plugins
	global $mmp;

	$mmp = new TC_MMP('mmp');
	$mmp->social = new TC_MMP_Share( 'y' );
	$mmp->newsletter = new TC_MMP_Newsletter();
	$mmp->login = new TC_MMP_Login();
	$mmp->lyrics = new TC_MMP_Lyrics();

	function is_ajax()
	{
		return (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
						&& strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
	}

?>