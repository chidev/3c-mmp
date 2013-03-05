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
	 * RECENT POSTS
	 * 
	 * 'categories'				allowed:	comma seperated string or array of ids/slugs
	 * 
	 * 'exclude_type'			allowed:	category_id (default), category_slug, post, 
	 * 															post_slug. accepts string/array
	 * 
	 * 'format'						allowed:	html, list, array
	 */
	if ( !function_exists('tc_recent_posts'))
	{
		function tc_recent_posts($args)
		{
			// Set default arguments
			$defaults = array(
				'limit'					=> 5,
				'categories'		=> array(),
				'exclude'				=> array(),
				'exclude_type'	=> 'category',
				'format'				=> 'array',
				'echo'					=> false
			);
			
			// Parse arguments
			$args = wp_parse_args( $args, $defaults );
			$args = apply_filters( 'tc_recent_posts_args', $args );
			$args = (object) $args;
			
			/**
			 * Setup arguments
			 */
			
			// Limit
			$args->limit = (int) $args->limit;
			$args->limit === 0 and $args->limit = 1;
			
			// Categories
			if ( is_string($args->categories) )
			{
				var_dump( wp_parse_args($args->catgegories, '') );
			}
			elseif ( !is_array($args->categories) )
			{
				var_dump( (array) $args->categories );
			}
			
			// Exclude
			
		}
	}
	
	/**
	 * GET POST CONTENT
	 */
	if ( !function_exists('get_the_content_with_formatting'))
	{
		function get_the_content_with_formatting()
		{
			global $more;
			$more = 0;
			$content = get_the_content('Read More &raquo;');
			$content = wpautop($content, $br = 1);
			$content = str_replace(']]>', ']]&gt;', $content);
			return $content;
		}
	}
	
	/**
	 * GENERATE EXCERPT
	 */
	if ( !function_exists('excerpt'))
	{
		function excerpt($num)
		{
			$limit = $num+1;  
			$excerpt = explode(' ', get_the_excerpt(), $limit);  
			array_pop($excerpt);  
			$excerpt = implode(" ",$excerpt)."...";  
			echo $excerpt;  
		}
	}
	
	/**
	 * LIMIT WORDS
	 */
	if ( !function_exists('limit_words'))
	{
		function limit_words($orig_text, $num, $chars = null)
		{
			$limit = $num+1;  
			$excerpt = explode(' ', $orig_text, $limit);  
			array_pop($excerpt);  
			$excerpt = implode(" ",$excerpt)."...";  
			return $excerpt;  
		}
	}
	
	/**
	 * GET POSTS TAGS
	 */
	if ( !function_exists('get_post_tags'))
	{
		function get_post_tags($num, $sep=", ", $do_echo=true)
		{
			$post_tags = get_the_tags($num);
			if ($post_tags)
			{
				foreach($post_tags as $tag) {
					$tag_html[] = '<a href="'. get_tag_link($tag) .  '">' . $tag->name . '</a>';
				}
			}
			$tags = implode($sep, $tag_html);
			
			if ($do_echo)
			{
				echo $tags;
			}
			else {
				return $post_tags;
			}
			
		}
	}
	
	/**
	 * IF POST HAS TAGS
	 */
	if ( !function_exists('has_tags'))
	{
		function has_tags($num)
		{
			if (get_the_tags($num))
			{
				return true;
			}
			else {
				return false;
			}
		}
	}
	
	function tc_title( $title, $max = 70, $last = '...' )
	{
		return tc_cut( $title, $max );
	}
	
	function tc_cut ( $str , $max = 70, $last = '...' )
	{
		$max = (int) $max;
		$max < 10 and $max = 10;
		$end = $max - 5;
		
		if ( strlen( $str ) > $max )
		{
			$words = explode( ' ', $str );
			$c = substr( $str, 0, $end );
			$strwords = explode( ' ', $c );
			$last_word = array_pop( $strwords );
			
			$str = rtrim( implode( ' ', $strwords ), '.,;-' );
			
			if ($last_word == $words[ ( count( $strwords ) ) ] )
			{
				$str .= $last_word;
			}
			
			if ($last) $str .= $last;
		}
		
		return $str;
	}
	
	function tc_excerpt( $p = false , $excerpt_length = 280, $last = '...', $wrap_p = true )
	{
		if ( ! $p)
		{
			global $post;
			$p = $post;
		}
		
		$content = $p->post_excerpt;
		
		if ( $content )
		{
			$content = apply_filters('the_excerpt', $content);
		}
		else {
			$content = $p->post_content;
			$content = strip_shortcodes($content);
			$content = str_replace(']]>', ']]&gt;', $content);
			$content = strip_tags($content);
		}
		
		$content = tc_cut( $content, $excerpt_length, $last );
		$wrap_p === true and $content = '<p>' . $content . '</p>';
		
		return $content;
	}
?>