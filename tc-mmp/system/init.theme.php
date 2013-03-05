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
	 * INITIALIZE THEME VARIABLES
	 */
	if (!function_exists('tc_init_theme'))
	{
		function tc_init_theme()
		{
			/**
			 * PREPARE ENVIRONMENT
			 */

			// Include required files
			include(TC_GLOBALS);

			// AJAX check
			$is_ajax = ($_GET['ajax'] == 'true' || (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'));

			// Prepare variables
			$page_classes = $js_sections = $js_vars = $js_arr = array();

			/**
			 * Conditional statements by page/section
			 */
			if ( is_home() )
			{
				$page_classes[] = $js_sections[] = 'home';
			}
			elseif ( is_single() )
			{
				$js_sections[] = 'single';
			}
			elseif ( is_page() )
			{
				$js_sections[] = 'page';
			}
			elseif ( is_category() )
			{
				$js_sections[] = 'category';
			}

			// Generate javascript for header
			$js_arr[] = 'var tc_sections = tc_sections || [];';
			$js_vars = array('home_uri' => get_bloginfo('url'), 'template_uri' => get_bloginfo('template_url'));
			if ( ! empty($js_vars))
			{
				$js_arr[] = 'var tc_var = tc_var || new _tc_var();';
				$js_arr[] = tc_js_vars($js_vars);
			}
			if (count($js_sections) === 1)
			{
				$js_arr[] = 'tc_sections.push("'.$js_sections[0].'");';
			}
			elseif (count($js_sections) > 1)
			{
				$js_arr[] = 'tc_sections.push("'.implode(',',$js_sections).'");';
			}
			$js_arr[] = empty($js_vars)
								? '_tc_init_sections($, tc_sections);'
								: '_tc_init_sections($, tc_sections, tc_var);';

			// Format javascript array into a string for use in theme
			$page_js = implode(' ', $js_arr);

		}
	}

	tc_init_theme();
?>
