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
	 * Wordpress
	 */
	global $post, $wp_query;
	global $post_id;
	global $post_name;
	global $parent_id, $parent_name, $parent_ids, $parents;
	global $slug;
	global $cat, $cat_name;
	
	/**
	 * Wordpress areas
	 */ 
	global $is_blog;
	global $is_tag;
	global $is_category;
	global $is_archive;
	global $is_author;
	global $is_date;
	global $is_day;
	global $is_month;
	global $is_year;

	/**
	 * Section specific
	 */ 
	global $is_home;
	
	/**
	 * Pods CMS
	 */
	global $pod_data;
	global $common_data;
	global $sortby, $orderby, $limit;
	
	/**
	 * 3rd Corner Studios CMS
	 */
	global $tc;
	global $cache;
	global $current_url, $abs_template_url, $path_slugs;
	global $tc_breadcrumbs, $tc_bc_override, $tc_bc_options;

	/**
	 * 3rd Corner Studios Plugins
	 */
	global $tc_plugin_core;
	global $tc_plugins;
	global $contact_form;
	global $album_info;
	/**
	 * Page & Theme
	 */
	global $tconf, $doc, $tc_added, $tc_stickies;
	global $page_meta, $page_class, $page_classes;
	global $nojs, $page_js, $js_sections, $js_vars;
	global $is_ajax;
	
?>