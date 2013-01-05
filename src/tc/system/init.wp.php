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
 * LOAD SCRIPTS / STYLES
 */
if (!function_exists('tc_externals'))
{
	function tc_externals()
	{
		global $tconf;

		if (!is_admin())
		{

			$version = time(); //tc_get_build_number();

			$js_head_url = get_bloginfo('template_url').'/js/tc.head.js.php';

			$js_foot_url = get_bloginfo('template_url').'/js/tc.js.php';

			$js_mobile_head_url = get_bloginfo('template_url').'/js/tc.headmobile.js.php';
			$js_mobile_foot_url = get_bloginfo('template_url').'/js/tc.mobile.js.php';

			$css_url = is_page( 'mobile' )
							 ? get_bloginfo('template_url').'/css/tc.cssmobile.php'
							 : get_bloginfo('template_url').'/css/tc.css.php';

			wp_deregister_script('jquery');

			$v = time(); // 5.5

			// Mobile
			if ( is_page( 'mobile' ) )
			{
				wp_register_script( 'jquery', $js_mobile_head_url, false, '1.6.2', 0 );
				wp_register_script( 'xv2_footer', $js_mobile_foot_url, array( 'jquery' ), $v, 2 );
			}

			// Desktop
			else {
				wp_register_script( 'jquery', $js_head_url, false, '1.6.2', 0 );
				wp_register_script( 'xv2_footer', $js_foot_url, array( 'jquery' ), $v, 1 );
			}

			wp_enqueue_script('jquery');
			wp_enqueue_script('xv2_footer');

			wp_enqueue_style('tc-style', $css_url, false, $version, 'all');
		}
	}
}

/**
 * LOAD AND FILL THEME VARIABLES
 */

if (!function_exists('tc_init'))
{
	function tc_init()
	{
		if (!is_admin())
		{
			// Load TC core
			$tc = TC::init();

			// Load site specific logic
			TC::load_site();
		}
		else {
			//include(TC_LIBPATH.'_post_types.php');
		}
	}
}

if (!function_exists('tc_do_init_theme'))
{
	function tc_do_init_theme()
	{
		if (!is_admin())
		{
			// Initialize theme
			include(TC_SYSTEM_PATH.'/init.theme.php');
		}
		else {
			//include(TC_LIBPATH.'_post_types.php');
		}
	}
}

/**
 * DEREGISTER UNWANTED ELEMENTS
 */
if (!function_exists('tc_deregister'))
{
	function tc_deregister()
	{
		if (!is_admin())
		{
			// Remove Really simple discovery link
			remove_action('wp_head', 'rsd_link');
			// Remove Windows Live Writer link
			remove_action('wp_head', 'wlwmanifest_link');
			// Remove the version number
			remove_action('wp_head', 'wp_generator');
			// Remove cooliris
			remove_action('wp_head', 'nggMediaRssadd_piclens_javascript');

			// Remove Windows Live Writer link
			remove_action('wp_head', 'wlwmanifest_link');
			// Remove the version number
			remove_action('wp_head', 'wp_generator');

			//wp_deregister_style('NextGEN');

			remove_action('wp_head', 'jcp_jquery');
			remove_action('wp_head', 'echo_script');

		}
	}
}

/**
 * DEREGISTER UNWANTED ELEMENTS
 */
if (!function_exists('tc_deregister_scripts'))
{
	function tc_deregister_scripts()
	{
		if (!is_admin())
		{

		}
	}
}

/**
 * DEREGISTER UNWANTED ELEMENTS
 */
if (!function_exists('tc_deregister_styles'))
{
	function tc_deregister_styles()
	{
		if (!is_admin())
		{
			//wp_deregister_style('NextGEN');
		}
	}
}

/**
 * DISABLE RSS
 */
function fb_disable_feed() {
	wp_die( __('No feed available,please visit our <a href="'. get_bloginfo('url') .'" title="PurlAgent - Social Networking for the Business Environment">PurlAgent - Social Networking for the Business Environment</a>!') );
}




/**
 * ADD HOOKS
 */
add_action('get_header',				'tc_externals');
add_action('init',							'tc_init', 100);
add_action('get_header',				'tc_deregister', 99);
add_action('get_header',				'tc_do_init_theme', 99);
add_action('wp_print_scripts',	'tc_deregister_scripts', 99999);
add_action('wp_print_styles',		'tc_deregister_styles', 99999);

// RSS related hooks
/*
add_action('do_feed',				'fb_disable_feed', 1);
add_action('do_feed_rdf',		'fb_disable_feed', 1);
add_action('do_feed_rss',		'fb_disable_feed', 1);
add_action('do_feed_rss2',	'fb_disable_feed', 1);
add_action('do_feed_atom',	'fb_disable_feed', 1);
*/

add_theme_support( 'post-thumbnails' );
set_post_thumbnail_size( 100, 75, true ); // Normal post thumbnails
add_image_size( 'image-gallery', 150, 300 );
?>