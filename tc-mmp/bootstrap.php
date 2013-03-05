<?php defined('ABSPATH') OR die('No direct access allowed');
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
	 * System setup
	 */
	include(dirname(__FILE__).'/system/init.globals.php');

	/**
	 * TC constants
	 */
	$tpl_cf = get_bloginfo('template_url');
	!defined('TEMPLATEURL')							and define('TEMPLATEURL',							get_bloginfo('template_url'));
	!defined('TEMPLATEURL_CF')					and define('TEMPLATEURL_CF',					$tpl_cf);
	!defined('TC_PLUGIN_TPL_PATH')			and define('TC_PLUGIN_TPL_PATH',			TEMPLATEPATH);
	!defined('TC_PLUGIN_TPL_URL')				and define('TC_PLUGIN_TPL_URL',				TEMPLATEURL);
	!defined('TC_PATH')									and define('TC_PATH',									TEMPLATEPATH.'/lib/tc');
	!defined('TC_SYSTEM_PATH')					and define('TC_SYSTEM_PATH',					TC_PATH.'/system');
	!defined('TC_LIBRARIES_PATH')				and define('TC_LIBRARIES_PATH',				TC_PATH.'/libraries');
	!defined('TC_PLUGINS_PATH')					and define('TC_PLUGINS_PATH',					TC_PATH.'/plugins');
	!defined('TC_APPLICATION_PATH')			and define('TC_APPLICATION_PATH',			TC_PATH.'/application');
	!defined('TC_CONFIG_PATH')					and define('TC_CONFIG_PATH',					TC_PATH.'/config');
	!defined('TC_CACHE_PATH')						and define('TC_CACHE_PATH',						TC_PATH.'/cache');
	!defined('TC_VENDOR_PATH')					and define('TC_VENDOR_PATH',					TC_PATH.'/vendor');
	!defined('TC_GLOBALS')							and define('TC_GLOBALS',							TC_SYSTEM_PATH.'/init.globals.php');
	!defined('TC_URL')									and define('TC_URL',									TEMPLATEURL.'/lib/tc');
	!defined('EXTERNAL_LIBPATH')				and define('EXTERNAL_LIBPATH',				TEMPLATEPATH.'/lib');
	!defined('EXTERNAL_LIBURL')					and define('EXTERNAL_LIBURL',					TEMPLATEURL.'/lib');
	!defined('EXTERNAL_LIBURL_CF')			and define('EXTERNAL_LIBURL_CF',			TEMPLATEURL_CF.'/lib');

	/**
	 * Detect mobile
	 */
	include(TC_VENDOR_PATH.'/detectmobilebrowser.php');

	/**
	 * Theme config array
	 */
	$tconf = array
	(
		'phpthumb_path'		=> EXTERNAL_LIBPATH.'/phpThumb1.7.9.',
		'phpthumb_url'		=> EXTERNAL_LIBURL_CF.'/phpThumb1.7.9/phpThumb.php',
		'minify_url'			=> EXTERNAL_LIBURL_CF.'/min',
		'fb_path'					=> EXTERNAL_LIBPATH.'/fb',
	);

	/**
	 * Initialize and load core classes
	 */

	include(TC_VENDOR_PATH.'/theme-options/theme-options.php');
	include(TC_SYSTEM_PATH.'/init.admin.php');
	include(TC_SYSTEM_PATH.'/init.wp.php');
	include(TC_SYSTEM_PATH.'/class.config.php');
	include(TC_SYSTEM_PATH.'/class.plugins.php');
	include(TC_SYSTEM_PATH.'/class.cache.php');
	include(TC_SYSTEM_PATH.'/class.tc.php');

	/**
	 * Load default CMS functions
	 */
	/*
	include(TC_LIBRARIES_PATH.'/functions.cms.php');
	include(TC_LIBRARIES_PATH.'/functions.debug.php');
	include(TC_LIBRARIES_PATH.'/functions.mail.php');
	include(TC_LIBRARIES_PATH.'/functions.misc.php');
	include(TC_LIBRARIES_PATH.'/functions.time.php');
	include(TC_LIBRARIES_PATH.'/functions.xml.php');
	include(TC_LIBRARIES_PATH.'/functions.win.php');
	*/

	/**
	 * Load social networking functions
	 */
	//include(TC_LIBRARIES_PATH.'/social.twitter.php');

	/**
	 * Load theme functions
	 */
	include(TC_LIBRARIES_PATH.'/theme.js.php');
	include(TC_LIBRARIES_PATH.'/theme.css.php');

	/**
	 * Load Wordpress functions
	 */
	/*
	include(TC_LIBRARIES_PATH.'/wp.comments.php');
	include(TC_LIBRARIES_PATH.'/wp.custom.php');
	include(TC_LIBRARIES_PATH.'/wp.general.php');
	include(TC_LIBRARIES_PATH.'/wp.posts.related.php');
	include(TC_LIBRARIES_PATH.'/wp.posts.php');
	include(TC_LIBRARIES_PATH.'/wp.widgets.php' );
	*/

	include(TC_APPLICATION_PATH.'/init.site.pre.php');

?>