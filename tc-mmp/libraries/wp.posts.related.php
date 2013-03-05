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
	 * RELATED POSTS SHORTCODE
	 */
	function related_posts_shortcode( $atts ) {
	 
		extract(shortcode_atts(array(
		    'limit' => '5',
		), $atts));
	 
		global $wpdb, $post, $table_prefix;
	 
		if ($post->ID) {
	 
			$retval = '
	<ul>';
	 
			// Get tags
			$tags = wp_get_post_tags($post->ID);
			$tagsarray = array();
			foreach ($tags as $tag) {
				$tagsarray[] = $tag->term_id;
			}
			$tagslist = implode(',', $tagsarray);
	 
			// Do the query
			$q = "
				SELECT p.*, count(tr.object_id) as count
				FROM $wpdb->term_taxonomy AS tt, $wpdb->term_relationships AS tr, $wpdb->posts AS p
				WHERE tt.taxonomy ='post_tag'
					AND tt.term_taxonomy_id = tr.term_taxonomy_id
					AND tr.object_id  = p.ID
					AND tt.term_id IN ($tagslist)
					AND p.ID != $post->ID
					AND p.post_status = 'publish'
					AND p.post_date_gmt < NOW()
				GROUP BY tr.object_id
				ORDER BY count DESC, p.post_date_gmt DESC
				LIMIT $limit;";
	 
			$related = $wpdb->get_results($q);
	 
			if ( $related ) {
				foreach($related as $r) {
					$retval .= '
		<li><a title="'.wptexturize($r->post_title).'" href="'.get_permalink($r->ID).'">'.wptexturize($r->post_title).'</a></li>
	';
				}
			} else {
				$retval .= '
		<li>No related posts found</li>
	';
			}
			$retval .= '</ul>
	';
			return $retval;
		}
		return;
	}
	
	add_shortcode('related_posts', 'related_posts_shortcode');
	
?>