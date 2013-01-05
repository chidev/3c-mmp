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
	 * Get breadcrumbs (based on URL path)
	 * loop through each slug and build breadcrumb array
	 *
	 * @param		bool		set to true if
	 * @param		bool		check if last slug is a child of the first slug
	 * @param		array		pass an array of override values for the bread crumbs
	 *									$override['slug'] = array('url' => $url, 'title' => $title);
	 *
	 * @param		array		pass an array of options you can set a custom home page link,
											otherwise default values from wordpress are used
	 *									$options['home'] = array('url' => $url, 'title' => $title);
	 */
	function tc_breadcrumbs($override = array(), $options = array(), $check_if_child_cat = FALSE, $is_pods_page = FALSE)
	{
		// set home link
		$home = (isset($options['home']['url']) && isset($options['home']['title'])) 
					? $options['home'] 
					: array('url' => get_bloginfo('url'), 'title' => get_bloginfo('name'));
				
		// get slugs from URL
		$bc_slugs = tc_slugs();
		
		$bc_url = get_bloginfo('url').'/';
		$bc_is_profile = FALSE;
		foreach ($bc_slugs as $path_slug)
		{
			$bc_url .= $path_slug.'/';
			if ($path_slug != 'profile')
			{
				$bc_title = str_replace(array('-','_'),' ', $path_slug);
				$bc_title = ucwords($bc_title);
				
				$breadcrumbs[$path_slug] = array
				(
					'url'			=> $bc_url,
					'title'		=> $bc_title
				);
			}
		}
		
		if (isset($override) && !empty($override)) {
			foreach ($override as $slug => $data) {
				$breadcrumbs[$slug] = $data;
			}
		}	
		return $breadcrumbs;
	}
	
	/**
	 * Get breadcrumbs based on Wordpress
	 */
	function tc_wp_breadcrumbs()
	{
		if (is_page() && !is_front_page() || is_single() || is_category()) {
				echo '<ul class="breadcrumbs">';
				echo '<li class="front_page"><a href="'.get_bloginfo('url').'">'.get_bloginfo('name').'</a></li>';
 
				if (is_page()) {
						$ancestors = get_post_ancestors($post);
 
						if ($ancestors) {
								$ancestors = array_reverse($ancestors);
 
								foreach ($ancestors as $crumb) {
										echo '<li><a href="'.get_permalink($crumb).'">'.get_the_title($crumb).'</a></li>';
								}
						}
				}
 
				if (is_single()) {
						$category = get_the_category();
						echo '<li><a href="'.get_category_link($category[0]->cat_ID).'">'.$category[0]->cat_name.'</a></li>';
				}
 
				if (is_category()) {
						$category = get_the_category();
						echo '<li>'.$category[0]->cat_name.'</li>';
				}
 
				// Current page
				if (is_page() || is_single()) {
						echo '<li class="current">'.get_the_title().'</li>';
				}
				echo '</ul>';
		} elseif (is_front_page()) {
				// Front page
				echo '<ul class="breadcrumbs">';
				echo '<li class="front_page"><a href="'.get_bloginfo('url').'">'.get_bloginfo('name').'</a></li>';
				echo '<li class="current">Home Page</li>';
				echo '</ul>';
		}
	}
	
	/**
	 * Create a url based on a slug
	 */ 
	function tc_child_url($id, $slug, $last_parent = 'Artists', $parent_slug = 'artists', $profile = FALSE) {
		global $cache;
		
		$slugs = array();
		
		// check in cache for url so we dont have to waste DB queries
		$cache_url = 0; //$cache->Get($last_parent.'_url_'.$id.'_'.$slug);
		
		if (!$cache_url)
		{
			step_up_slugs($id, $slugs, $last_parent);
			
			// build url
			$url = get_bloginfo('url').'/'.$parent_slug;
			foreach ($slugs as $slug_data)
			{
				$url .= '/'.$slug_data['slug'];
			}
			
			$url .= ($profile) ? '/profile/'.$slug : '/'.$slug;
			
			//$cache->Set('url_'.$last_parent.'_'.$id.'_'.$slug,  $url);
		}
		
		// load url from cache
		else {
			$url = $cache_url;
		}
		
		return $url;
	}
	
	
	
	/**
	 * Recursive function for filling an array with slugs.
	 */
	function step_up_slugs($id, &$slugs = array(), $last_parent) {
		$sql = "
		SELECT
				tx.parent, t.name, t.slug
		FROM
				@wp_term_taxonomy tx
		INNER JOIN
				@wp_terms t ON t.term_id = tx.term_id
		WHERE
				tx.term_id = $id AND tx.taxonomy = 'category'
		LIMIT 1
		";
		
		$result = pod_query($sql);
		while ($row = mysql_fetch_assoc($result)) {
			if ($row['name'] == $last_parent) {
				$slugs = array_reverse($slugs);
			} else {
				$slugs[] = $row;
				step_up_slugs($row['parent'], $slugs, $last_parent);
			}
		}
	}
	
	
	
	/**
	 * Get current URL
	 *
	 * @return	current URL
	 */
	function tc_get_current_url() {
			$protocol = 'http';
			if ($_SERVER['SERVER_PORT'] == 443 || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')) {
					$protocol .= 's';
					$protocol_port = $_SERVER['SERVER_PORT'];
			} else {
					$protocol_port = 80;
			}
			$host = $_SERVER['HTTP_HOST'];
			$port = $_SERVER['SERVER_PORT'];
			$request = rtrim($_SERVER['REQUEST_URI'], '/');
			$query = substr($_SERVER['argv'][0], strpos($_SERVER['argv'][0], ';'));
			$toret = $protocol . '://' . $host . ($port == $protocol_port ? '' : ':' . $port) . $request; // . (empty($query) ? '' : '?' . $query);
			
			return $toret;
	}
	
		
		
	/**
	 * GET CHILDREN
	 */
	function build_children($parent_id,$cnt=1) {
		global $wpdb, $childrenA;
		$parentQ = "SELECT term_id FROM wp_term_taxonomy WHERE taxonomy = 'category' AND parent = '$parent_id'";
		$children = $wpdb->get_results($parentQ, ARRAY_A);
		$cnt++;
		if ($cnt < 200 && $children) {
			foreach($children as $child) {
				if ($child['term_id'] != $parent_id) {
					array_push($childrenA, $child['term_id']);
					$tmpA = build_children($child['term_id'],$cnt);
					array_merge($childrenA, $tmpA);
					//echo $cnt .'-'. $child['term_id'].'<hr>';
				}
			}
		}
		return $childrenA;
	}
	

	/**
	 * Determine if a slug is a child in the wordpress taxanomy hierarchy
	 * If no values are passed they are determined automatigically from the current URL
	 *
	 * @param		string		slug to check
	 * @param		array			array to traverse
	 * @reutrn	bool
	 */
	function is_wp_cat_child($top_parent = NULL, $slug_id = NULL, $slug_index = NULL)
	{
		$status = NULL;
		$slug_index = ($slug_index) 
								? array_reverse($slug_index) 
								: array_reverse(tc_slugs());
		
		
		
		$top_parent = ($top_parent) ? $top_parent : array_pop($slug_index);
		$slug_id = ($slug_id) ? $slug_id : $slug_index[0];
		$i = 1;
		
		// check if $slug is a child term of the 'artists' category
		while ($status === NULL)
		{			
			// if it's a string we're just getting the parent ID.
			if (is_string($slug_id))
			{
				$sql = "
					SELECT
							tx.parent 
					FROM
							@wp_term_taxonomy tx
					INNER JOIN
							@wp_terms t ON t.term_id = tx.term_id
					WHERE
							t.slug = '$slug_id' AND tx.taxonomy = 'category' 
					LIMIT 1
					";
				
				$result = pod_query($sql);
				$row = mysql_fetch_assoc($result);
				
				if (!empty($row) && isset($row['parent'])) {
					$slug_id = (int)$row['parent'];
				} else {
					$status = FALSE;
				}


			}
			
			// otherwise we have a parent id already 
			elseif (is_integer($slug_id)) {
				$sql = "
					SELECT
							tx.parent, t.slug
					FROM
							@wp_term_taxonomy tx
					INNER JOIN
							@wp_terms t ON t.term_id = tx.term_id
					WHERE
							t.term_id = $slug_id AND tx.taxonomy = 'category' 
					LIMIT 1
					";
				
				$result = pod_query($sql);
				$row = mysql_fetch_assoc($result);
				
				// if slug == 'artist' break with a good status, slug IS a child of the 'artist' term
				if (isset($row['slug']) && $row['slug'] == $top_parent) {
					$status = TRUE;
					break;
				}
					
				// check for row & make sure slug matches path
				elseif (
					!empty($row) 
					&& isset($row['parent']) 
					&& isset($row['slug']) 
					&& isset($slug_index[$i]) 
					&& $row['slug'] == $slug_index[$i])
				{
					$slug_id = (int)$row['parent'];
				}
				
				else {
					$status = FALSE;
				}
				
				$i++;
			}
		}
		
		return $status;
	}
	
	
	
	/**
	 * Get an array of slugs based on the current URL
	 * 
	 * @param		string					url to break down into slugs
	 * @return	array|bool		returns array of slugs or FALSE
	 */
	function tc_slugs($current_url = NULL) {
		$current_url = ($current_url) ? $current_url : get_current_url();
		$url_pieces = explode('?', $current_url);
		$url_pieces = str_replace(get_bloginfo('url').'/', '', $url_pieces[0]);
		$path_slugs = explode('/', $url_pieces);
		
		return (count($path_slugs) > 0 && !empty($path_slugs))
					 ? $path_slugs 
					 : FALSE;
	}
	
	
	/**
	 * Turn a slug into the page title, replaces - and _
	 *
	 * @param  string  slug
	 * @return string  returns formatted string w/ ucwords
	 */
	function tc_clean_slug($title) {
		$title = strtolower(str_replace(array('-','_'), ' ', $title));
		$title = ucwords($title);
		return $title;
	}
	
	/**
	 * 
	 */
	function tc_build_title($slugs, $sep = ' | ', $add = 'name') {
		$title = implode($sep, array_reverse($slugs));
		$title = clean_title($title);
		$add !== FALSE and $title .= $sep.get_bloginfo($add);
		return $title;
	}
	
	function get_abs($path) {
		$abs = explode('//', $path);
		$pos = strpos($abs[1], '/');
		$abs = substr($abs[1], $pos, strlen($abs[1]));
		return ($abs[0] == '/') ? $abs : '/'.$abs;
	}
	
	function tc_nav_menu($args)
	{
		$defaults = array('echo' => true, 'show_home' => true, 'add_span' => true, 'add_sep' => false, 'title_li' => '');
		$args = wp_parse_args( $args, $defaults );
		$args = apply_filters( 'tc_nav_menu_args', $args );
		$args = (object) $args;
		
		$do_wp_menu = function_exists('wp_nav_menu');
		
		if ($do_wp_menu)
		{
			$menu_args = array('echo' => false, 'menu' => $args->menu);
			
			$menu = wp_nav_menu($menu_args);
		}
		else {
			$menu_args = 'meta_key=tc_menu_inclue&meta_value='.$args->menu.'&depth=1&echo=0&link_before=TCstartaspantaghere&link_after=TCendaspantaghere&title_li=';
			
			$menu = wp_list_pages($menu_args);
		}
		
		// add span
		if ($args->add_span)
		{
			$menu = $menu = ($do_wp_menu)
						? preg_replace('/<a([^>]*)>([^<\/]*)<\/a>/i', '<a$1><span>$2</span></a>', $menu) 
						: str_replace(array('TCstartaspantaghere', 'TCendaspantaghere'), array('<span>', '</span>'), $menu);
		}
		
		// add home
		if ($args->show_home)
		{
			$home_class = (is_home()) ? ' class="current_page_item"' : '';
			
			$menu = ($do_wp_menu)
						? preg_replace('/<ul([^>]*)>/', '<ul$1><li'.$home_class.'><a href="'.get_bloginfo('url').'" title="'.get_bloginfo('name').'"><span>Home</span></a></li>', $menu) 
						: '<li'.$home_class.'><a href="'.get_bloginfo('url').'" title="'.get_bloginfo('name').'"><span>Home</span></a></li>'.$menu;
		}
		
		// add seperators
		if ($args->add_sep)
		{
			$menu = str_replace('</li><li', '</li><li>&nbsp;|&nbsp;</li><li', $menu);
		}
		
		// add ul wrapper if we're using wp_list_pages
		if ( ! $do_wp_menu)
		{
			$menu = '<ul class="wp_menu" id="tc_menu_'.$args->menu.'">'.$menu.'</ul>';
		}
		
		if ($args->echo)
		{
			echo $menu;
		}
		else {
			return $menu;
		}
	}
	
	
	
	function tc_pod_pages($dir = null)
	{
		$dir = file_exists($dir);
		
		if ( ! $dir)
		{
			$dir = TEMPLATEPATH;
		}
		
		$mask = 'p-*.php';
		
		$pods = array();
		
		foreach (glob($dir.'/'.$mask) as $page)
		{
			$pods[] = str_replace(array('p-','-'), array('', '_'), $page);
		}
		
		return $pods;
	}
?>
