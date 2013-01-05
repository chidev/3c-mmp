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
	
	
	if ( ! $posts = TC::$cache->get('pa-blog-recent-posts'))
	{
		$posts = array();
		include_once(ABSPATH.WPINC.'/rss.php');
		$footer_rss = fetch_rss('http://www.graphics.ms/?feed=rss2');
		
		if ($footer_rss)
		{
			if ($footer_rss && count($footer_rss->items) > 3)
				$footer_rss->items = array_slice( (array)$footer_rss->items, 0, 3 );
			
			$footer_items = (array)$footer_rss->items;
			
			foreach ( $footer_items as $item ) {
				$posts[] = array(
					'title'							=> htmlentities( $item['title'] ),
					'link'							=> esc_url( $item['link'] ),
					'description'				=> limit_words( esc_attr( strip_tags( $item['description'] ) ), 15)
				);
			}
			$cache->set('pa-blog-recent-posts', $posts, 86400);
		}
	}
	
?>