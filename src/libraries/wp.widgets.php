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
	
	/*** List of widgets: ***/
	function tc_widgets_init()
	{
		register_widget('Widget_TC_Recent_Tweets');
		register_widget('Widget_TC_Custom_Archives');
		register_widget('Widget_TC_Custom_Calendar');
		register_widget('Widget_TC_Featured_Post');
		/*
		register_widget('Widget_Custom_Recent_Posts');
		register_widget('Widget_Menu');
		register_widget('Widget_Search');
		register_widget('Widget_Newsletter');
		register_widget('Widget_Social_Icons');
		register_widget('Widget_Social_Badges');
		register_widget('Widget_Popular_Posts');
		register_widget('Widget_Sticky_Posts');
		register_widget('Widget_Featured_Post');
		*/
	}



	/**
	 * TC Dynamic Content
	 *
	class TC_Content_Widget extends WP_Widget
	{
		function TC_Content_Widget()
		{
			$widget_ops = array('classname' => 'widget_tc_content', 'description' => __('Add a TC theme section'));
			
			$control_ops = array('width' => 400, 'height' => 350);
			
			$this->WP_Widget('widget_tc_content', __('TC Content'), $widget_ops, $control_ops);
		}
	
		function widget( $args, $instance )
		{
			extract($args);
			
			$title = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance );
			
			$text = apply_filters( 'widget_execphp', $instance['text'], $instance );
			
			echo $before_widget;
			
			if ( !empty( $title ) ) { echo $before_title . $title . $after_title; } 
				ob_start();
				eval('?>'.$text);
				$text = ob_get_contents();
				ob_end_clean();
				?>			
				<div class="execphpwidget"><?php echo $instance['filter'] ? wpautop($text) : $text; ?></div>
			<?php
			echo $after_widget;
		}
	
		function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			$instance['title'] = strip_tags($new_instance['title']);
			if ( current_user_can('unfiltered_html') )
				$instance['text'] =  $new_instance['text'];
			else
				$instance['text'] = stripslashes( wp_filter_post_kses( $new_instance['text'] ) );
			$instance['filter'] = isset($new_instance['filter']);
			return $instance;
		}
	
		function form( $instance ) {
			$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '', 'content_type' => '' ) );
			$title = strip_tags($instance['title']);
			$text = format_to_edit($instance['text']);
			$content_type = strip_tags($instance['content_type']);
			$content_types = 
	?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
	
			<textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>
	
			<p><input id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="checkbox" <?php checked(isset($instance['filter']) ? $instance['filter'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('filter'); ?>"><?php _e('Automatically add paragraphs.'); ?></label></p>
	<?php
		}
	}
	*/


	/**
	 * Featured Posts+
	 */
	class Widget_TC_Featured_Post extends WP_Widget
	{
		/**
		 * Widget Options
		 */
		function Widget_TC_Featured_Post()
		{
			$widget_ops = array('classname' => 'widget_featured_psots', 'description' => __( 'Display posts from a category') );
			$this->WP_Widget('featured_posts', __('Featured Posts+'), $widget_ops);
		}
		
		/**
		 * Build Widget - Widget_TC_Featured_Post
		 */
		function widget( $args, $instance )
		{
			extract($args);
			
			$title = apply_filters('widget_title', empty($instance['title']) ? __('Featured Posts+') : $instance['title']);
			
			$args['title'] = $title;
			
			echo $before_widget;
			
			if ( !empty($title) ) { echo $before_title . $title . $after_title; }
			
			if ( ! $ft_posts = TC::cache_get('featured_posts_') or (int)$instance['use_cache'] === 0)
			{
				$sticky = get_option('sticky_posts');

				// Posts
				$args = array(
					'post_type'		=> 'post',
					'category__in'		=> array($instance['section']),
					'post__in'		=> $sticky,
				);
				$the_query = new WP_Query($args);
				
echo '<div style="padding:20px; background:#efefef;font-size:9px;color:#333;text-align:left;"><pre>';
var_dump($sticky,$the_query);
echo '</pre></div>';

				$ft_posts['primary'] = '';
				$ft_posts['secondary'] = '';
				
				//(int)$instance['use_cache'] !== 0 and TC::cache_set('featured_posts_');
			}
			
			echo $ft_posts['primary'];
			echo $ft_psots['secondary'];
			
			echo $after_widget;
		} // end widget()
		
		/**
		 * Update - Widget_TC_Featured_Post
		 */
		function update( $new_instance, $old_instance )
		{
			$instance = $old_instance;
			
			//$new_instance = wp_parse_args( (array) $old_instance, array( 'title' => '', 'section' => '', 'ft_post' => '', 'limit' => '', 'use_cache' => '', 'display_format' => '' ) );
			
			$instance['title'] = strip_tags($new_instance['title']);
			$instance['section'] = strip_tags($new_instance['section']);
			$instance['use_cache'] = strip_tags($new_instance['use_cache']);
			$instance['display_format'] = strip_tags($new_instance['display_format']);
			$instance['limit'] = (int)strip_tags($new_instance['limit']);
			
			return $instance;
		} // end update()
		
		/**
		 * Form - Widget_TC_Featured_Post
		 */
		function form( $instance )
		{

			$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'section' => '', 'ft_post' => '', 'limit' => '', 'use_cache' => 30, 'display_format' => '' ) );
			$title = strip_tags($instance['title']);
			$section = strip_tags($instance['section']);
			//$ft_post = strip_tags($instance['ft_post']);
			$limit = strip_tags($instance['limit']);
			$use_cache = strip_tags($instance['use_cache']);
			
			//$display_format = strip_tags($instance['display_format']);
			?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('leave blank to auto generate'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
			</p>
			<p class="tc_posts_plus_dd">
				<label for="<?php echo $this->get_field_id('cat'); ?>"><?php _e('Category:'); ?></label>
					<?
					/*
					$c = get_category_by_slug('sections'); $e = get_category_by_slug('uncategorized'); 
					$id = ($c) ? $c->term_id : ''; $eid = ($e) ? $e->term_id : '';
					*/
					$dd = wp_dropdown_categories('show_option_all=1&name='.$this->get_field_name('section').'&selected='.$section.'&hide_empty=0&hierarchical=1&depth=0&show_count=1&exclude=1&echo=0');
					$add = 'id="'.$this->get_field_id('sections').'" ';
					$dd = str_replace('<select', '<select '.$add, $dd);
					echo $dd;
					/*
					$icon = get_bloginfo('template_url').'/img/favicon.ico';
					echo '<div class="font10" style="font-size:10px;">'
								.wp_list_categories('name=section&selected='.$cat.'&hide_empty=0&hierarchical=1&depth=0&show_count=1&exclude=1&echo=0&feed_image='.$icon)
								.'</div>';
					*/
					?>
			</p>
			<script type="text/javascript">
				jQuery(document).ready(function($){
					$('.tc_posts_plus_dd select').change(function(){
						console.log("changed");
						var $str = $(this).serialize().split('=');
						var $frm = $(this).parent('form');
						var $sec = $('#tc-ft-post-section-input', $frm[0]);
						if ($sec.length)
						{
							$sec.attr('value', $str[1]);
							console.log("did it");
						}
						else {
							$frm.prepend('<input type="hidden" name="<?php echo $this->get_field_name('section'); ?>" id="<?php echo $this->get_field_id('section'); ?>" value="'+$str[1]+'" />');
						}
					});
				});
			</script>
			<p>
				<label for="<?php echo $this->get_field_id('limit'); ?>"><?php _e( 'Limit:' ); ?></label>
				<select name="<?php echo $this->get_field_name('limit'); ?>" id="<?php echo $this->get_field_id('limit'); ?>" class="widefat">
					<option value="3"<?php selected( $limit, '3' ); ?>><?php _e('3'); ?></option>
					<option value="4"<?php selected( $limit, '4' ); ?>><?php _e('4'); ?></option>
					<option value="5"<?php selected( $limit, '5' ); ?>><?php _e('5'); ?></option>
					<option value="8"<?php selected( $limit, '8' ); ?>><?php _e('8'); ?></option>
					<option value="10"<?php selected( $limit, '10' ); ?>><?php _e('10'); ?></option>
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('use_cache'); ?>"><?php _e('Cache for x minutes, 0 for none'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('use_cache'); ?>" name="<?php echo $this->get_field_name('use_cache'); ?>" type="text" value="<?php echo esc_attr($use_cache); ?>" />
			</p>
<?/*
			<p>
				<label for="<?php echo $this->get_field_id('display_format'); ?>"><?php _e( 'Display Format:' ); ?></label>
				<select name="<?php echo $this->get_field_name('display_format'); ?>" id="<?php echo $this->get_field_id('display_format'); ?>" class="widefat">
					<option value="ul"<?php selected( $instance['display_format'], 'ul' ); ?>><?php _e('List (&lt;ul&gt;)'); ?></option>
					<option value="ol"<?php selected( $instance['display_format'], 'ol' ); ?>><?php _e('List (&lt;ol&gt;)'); ?></option>
					<option value="div"<?php selected( $instance['display_format'], 'div' ); ?>><?php _e('Divs'); ?></option>
				</select>
			</p>
*/?>
			<?php
		} // end form()
	}
	// end class Widget_TC_Featured_Post
	
	
	
	/**
	 * Recent Tweets
	 */
	class Widget_TC_Recent_Tweets extends WP_Widget
	{
		/**
		 * Widget Options
		 */
		function Widget_TC_Recent_Tweets()
		{
			$widget_ops = array('classname' => 'widget_recent_tweets', 'description' => __( 'Grab the most recent tweets from your twitter feed') );
			$this->WP_Widget('recent_tweets', __('Recent Tweets'), $widget_ops);
		}
		
		/**
		 * Build Widget - Widget_TC_Recent_Tweets
		 */
		function widget( $args, $instance )
		{
			extract($args);
			
			$title = apply_filters('widget_title', empty($instance['title']) ? __('Recent Tweets') : $instance['title']);
			
			$args['title'] = $title;
			
			echo $before_widget;
			
			if ( !empty($title) ) { echo $before_title . $title . $after_title; }
			
			echo tc_twitter_recent($args);
			
			echo $after_widget;
		} // end widget()
		
		/**
		 * Update - Widget_TC_Recent_Tweets
		 */
		function update( $new_instance, $old_instance )
		{
			$instance = $old_instance;
			/*$new_instance = wp_parse_args( (array) $new_instance, array( 'title' => '', 'username' => '', 'limit' => '', 'use_cache' => '', 'use_avatar' => 0, 
				'link_users' => 0, 'link_tweet' => 0, 'link_users' => 0, 'link_hashes' => 0, 'display_format' => '') );
				*/
			$instance['title'] = strip_tags($new_instance['title']);
			$instance['username'] = strip_tags($new_instance['username']);
			$instance['use_cache'] = strip_tags($new_instance['use_cache']);
			$instance['use_avatar'] = $new_instance['use_avatar'] ? 1 : 0;
			$instance['link_users'] = $new_instance['link_users'] ? 1 : 0;
			$instance['link_tweet'] = $new_instance['link_tweet'] ? 1 : 0;
			$instance['link_hashes'] = $new_instance['link_hashes'] ? 1 : 0;
			$instance['display_format'] = strip_tags( $new_instance['display_format'] );
			$limit = (int)strip_tags($new_instance['type']);
			if ( in_array( $limit, array( 5, 10, 15, 20 ) ) )
			{
				$instance['type'] = $limit;
			}
			else {
				$instance['type'] = 5;
			}
			return $instance;
		} // end update()
		
		/**
		 * Form - Widget_TC_Recent_Tweets
		 */
		function form( $instance )
		{
			$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'username' => '', 'limit' => 0, 'use_cache' => '', 'use_avatar' => 0, 
				'link_users' => 0, 'link_tweet' => 0, 'link_users' => 0, 'link_hashes' => 0, 'display_format' => '') );
			$title = strip_tags($instance['title']);
			$username = strip_tags($instance['username']);
			$use_cache = strip_tags($instance['use_cache']);
			$use_avatar = ($instance['use_avatar'] == 1) ? 'checked="checked"' : '';
			$link_users = ($instance['link_users'] == 1) ? 'checked="checked"' : '';
			$link_tweet = ($instance['link_tweet'] == 1) ? 'checked="checked"' : '';
			$link_hashes = ($instance['link_hashes'] == 1) ? 'checked="checked"' : '';
			?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('username'); ?>"><?php _e('Twitter Username:'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('username'); ?>" name="<?php echo $this->get_field_name('username'); ?>" type="text" value="<?php echo esc_attr($username); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('limit'); ?>"><?php _e( 'Limit:' ); ?></label>
				<select name="<?php echo $this->get_field_name('limit'); ?>" id="<?php echo $this->get_field_id('limit'); ?>" class="widefat">
					<option value="5"<?php selected( $instance['limit'], '5' ); ?>><?php _e('5'); ?></option>
					<option value="10"<?php selected( $instance['limit'], '10' ); ?>><?php _e('10'); ?></option>
					<option value="15"<?php selected( $instance['limit'], '10' ); ?>><?php _e('15'); ?></option>
					<option value="20"<?php selected( $instance['limit'], '10' ); ?>><?php _e('20'); ?></option>
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('use_cache'); ?>"><?php _e('Cache for x minutes, 0 for none'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('use_cache'); ?>" name="<?php echo $this->get_field_name('use_cache'); ?>" type="text" value="<?php echo esc_attr($use_cache); ?>" />
			</p>
			<p>
				<input class="checkbox" type="checkbox" <?php echo $use_avatar; ?> id="<?php echo $this->get_field_id('use_avatar'); ?>" name="<?php echo $this->get_field_name('use_avatar'); ?>" /> 
				<label for="<?php echo $this->get_field_id('use_avatar'); ?>"><?php _e('Show Twitter avatar?'); ?></label>
				<br />
				
				<input class="checkbox" type="checkbox" <?php echo $link_users; ?> id="<?php echo $this->get_field_id('link_users'); ?>" name="<?php echo $this->get_field_name('link_users'); ?>" /> 
				<label for="<?php echo $this->get_field_id('link_users'); ?>"><?php _e('Link to @usernames'); ?></label>
				<br />
				
				<input class="checkbox" type="checkbox" <?php echo $use_avatar; ?> id="<?php echo $this->get_field_id('link_tweet'); ?>" name="<?php echo $this->get_field_name('link_tweet'); ?>" /> 
				<label for="<?php echo $this->get_field_id('link_tweet'); ?>"><?php _e('Links to tweet itself (status)'); ?></label>
				<br />
				
				<input class="checkbox" type="checkbox" <?php echo $use_avatar; ?> id="<?php echo $this->get_field_id('use_avatar'); ?>" name="<?php echo $this->get_field_name('use_avatar'); ?>" /> 
				<label for="<?php echo $this->get_field_id('use_avatar'); ?>"><?php _e('Links hashes to a Twitter search in a new window, uses &quot;nofollow&quot;'); ?></label>
				<br />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('display_format'); ?>"><?php _e( 'Display Format:' ); ?></label>
				<select name="<?php echo $this->get_field_name('display_format'); ?>" id="<?php echo $this->get_field_id('display_format'); ?>" class="widefat">
					<option value="ul"<?php selected( $instance['display_format'], 'ul' ); ?>><?php _e('List (&lt;ul&gt;)'); ?></option>
					<option value="ol"<?php selected( $instance['display_format'], 'ol' ); ?>><?php _e('List (&lt;ol&gt;)'); ?></option>
					<option value="div"<?php selected( $instance['display_format'], 'div' ); ?>><?php _e('Divs'); ?></option>
				</select>
			</p>
			<?php
		} // end form()
	}
	// end class Widget_TC_Recent_Tweets
	
	
	
	/**
	 * Archives widget class
	 */
	class Widget_TC_Custom_Archives extends WP_Widget
	{
		/**
		 * Widget Options
		 */
		function Widget_TC_Custom_Archives()
		{
			$widget_ops = array('classname' => 'widget_tc_custom_archives', 'description' => __( 'A monthly, yearly, day-by-day, or post-by-post archive of your blog&#8217;s posts') );
			$this->WP_Widget('tc_custom_archives', __('Custom Archives'), $widget_ops);
		}
		
		/**
		 * Build Widget - Widget_TC_Custom_Archives
		 */
		function widget( $args, $instance )
		{
			extract($args);
			$c = $instance['count'] ? '1' : '0';
			$d = $instance['dropdown'] ? '1' : '0';
			$title = apply_filters('widget_title', empty($instance['title']) ? __('Archives') : $instance['title']);
			$limit = empty( $instance['limit'] ) ? '' : $instance['limit'];
			$categories = empty( $instance['categories'] ) ? 'all' : $instance['categories'];
			$inc_child = empty( $instance['inc_child'] ) ? '1' : $instance['inc_child'];
			$type = $instance['type'];
			switch($type)
			{
				case "yearly":
					$type_nicename = "Year"; 
					break;
				case "monthly":
					$type_nicename = "Month"; 
					default;
					break;
				case "daily":
					$type_nicename = "Day"; 
					break;
				case "weekly":
					$type_nicename = "Weekly"; 
					break;
				case "postbypost":
					$type_nicename = "Post"; 
					break;
			}
			if ($categories == 'all')
			{
				global $wpdb;
				$catQ = "SELECT term_id FROM wp_term_taxonomy WHERE taxonomy = 'category'";
				$catR = $wpdb->get_results($catQ, ARRAY_A);
				if ($catR)
				{
					foreach ($catR as $cat)
					{
						$catA[] = $cat['term_id'];
					}
					$categories = implode(',',$catA);
				}
			}
			else {
				if ($inc_child)
				{
					global $wpdb, $childrenA;
					$catA = explode(',',$categories);
					$categoriesA = $catA;
					foreach($catA as $check_cat) {
						$childrenA = array();
						$childrenA = build_children($check_cat);
						$categoriesA = array_merge($categoriesA,$childrenA);
					}
					$categories = implode(',',$categoriesA);
				}
			}
			
			echo $before_widget;
			if ( $title )
				echo $before_title . $title . $after_title;
			
			if ( $d ): ?>
			<select name="archive-dropdown" onchange='document.location.href=this.options[this.selectedIndex].value;'>
				<option value=""><?php echo esc_attr(__('Select '.$type_nicename.'')); ?></option>
				<?php get_archives_custom(apply_filters('widget_archives_dropdown_args', array('cat' => $categories, 'type' => $type, 'format' => 'option', 'show_post_count' => $c, 'limit' => $limit))); ?>
			</select>
			<?php else: ?>
			<ul>
				<?php get_archives_custom(apply_filters('widget_archives_dropdown_args', array('cat' => $categories, 'type' => $type, 'show_post_count' => $c, 'limit' => $limit))); ?>
			</ul>
			<?php endif; 
			
			echo $after_widget;
		} // end widget()
		
		/**
		 * Update - Widget_TC_Custom_Archives
		 */
		function update( $new_instance, $old_instance )
		{
			$instance = $old_instance;
			$new_instance = wp_parse_args( (array) $new_instance, array( 'title' => '', 'categories' => '', 'limit' => '', 'type' => '', 'inc_child' => 0, 'count' => 0, 'dropdown' => '') );
			$instance['title'] = strip_tags($new_instance['title']);
			$instance['count'] = $new_instance['count'] ? 1 : 0;
			$instance['dropdown'] = $new_instance['dropdown'] ? 1 : 0;
			$instance['categories'] = strip_tags( $new_instance['categories'] );
			$instance['limit'] = strip_tags( $new_instance['limit'] );
			if ( in_array( $new_instance['type'], array( 'yearly', 'monthly', 'weekly', 'daily', 'postbypost' ) ) )
			{
				$instance['type'] = $new_instance['type'];
			}
			else {
				$instance['type'] = 'monthly';
			}
			return $instance;
		}
		// end update()
		
		/**
		 * Form - Widget_TC_Custom_Archives
		 */
		function form( $instance )
		{
			$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'categories' => '', 'limit' => '', 'type' => '', 'inc_child' => 1, 'count' => 0, 'dropdown' => '') );
			$title = strip_tags($instance['title']);
			$categories = strip_tags($instance['categories']);
			$limit = strip_tags($instance['limit']);
			$count = ($instance['count'] == 1) ? 'checked="checked"' : '';
			$dropdown = ($instance['dropdown'] == 1) ? 'checked="checked"' : '';
			$inc_child = ($instance['inc_child'] == 1) ? 'checked="checked"' : '';
			?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
			<p><label for="<?php echo $this->get_field_id('limit'); ?>"><?php _e('Limit:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="text" value="<?php echo esc_attr($limit); ?>" /></p>
			<p>
				<label for="<?php echo $this->get_field_id('categories'); ?>"><?php _e('Categories:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('categories'); ?>" name="<?php echo $this->get_field_name('categories'); ?>" type="text" value="<?php echo esc_attr($categories); ?>" />
				<br/>
				<small><?php _e('Category IDs, seperated by commas.');?></small>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('type'); ?>"><?php _e( 'Type:' ); ?></label>
				<select name="<?php echo $this->get_field_name('type'); ?>" id="<?php echo $this->get_field_id('type'); ?>" class="widefat">
					<option value="yearly"<?php selected( $instance['type'], 'yearly' ); ?>><?php _e('Yearly'); ?></option>
					<option value="monthly"<?php selected( $instance['type'], 'monthly' ); ?>><?php _e('Monthly'); ?></option>
					<option value="weekly"<?php selected( $instance['type'], 'weekly' ); ?>><?php _e('Weekly'); ?></option>
					<option value="daily"<?php selected( $instance['type'], 'daily' ); ?>><?php _e('Daily'); ?></option>
					<option value="postbypost"<?php selected( $instance['type'], 'postbypost' ); ?>><?php _e('Post by Post'); ?></option>
				</select>
			</p>		
			<p>
				<input class="checkbox" type="checkbox" <?php echo $count; ?> id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" /> <label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Show post counts'); ?></label>
				<br />
				<input class="checkbox" type="checkbox" <?php echo $dropdown; ?> id="<?php echo $this->get_field_id('dropdown'); ?>" name="<?php echo $this->get_field_name('dropdown'); ?>" /> <label for="<?php echo $this->get_field_id('dropdown'); ?>"><?php _e('Display as a drop down'); ?></label>
				<br />
				<input class="checkbox" type="checkbox" <?php echo $inc_child; ?> id="<?php echo $this->get_field_id('inc_child'); ?>" name="<?php echo $this->get_field_name('inc_child'); ?>" /> <label for="<?php echo $this->get_field_id('inc_child'); ?>"><?php _e('Include children'); ?></label>
			</p>
			<?php
		}
		// end form()
	}
	// end class Widget_TC_Custom_Archives
	
	
	
	/**
	 * Calendar widget class
	 */
	class Widget_TC_Custom_Calendar extends WP_Widget
	{	
		/**
		 * Widget Options
		 */
		function Widget_TC_Custom_Calendar()
		{
			$widget_ops = array('classname' => 'widget_tc_custom_calendar', 'description' => __( 'A calendar of your blog&#8217;s posts defined by categories') );
			$this->WP_Widget('tc_custom_calendar', __('Custom Calendar'), $widget_ops);
		}
		
		/**
		 * Build Widget - Widget_TC_Custom_Calendar
		 */
		function widget( $args, $instance )
		{
			extract($args);
			$title = apply_filters('widget_title', empty($instance['title']) ? '&nbsp;' : $instance['title']);
			$categories = empty( $instance['categories'] ) ? 'all' : $instance['categories'];
			$inc_child = empty( $instance['inc_child'] ) ? '1' : $instance['inc_child'];
	
			if ($categories == 'all') {
				global $wpdb;
				$catQ = "SELECT term_id FROM wp_term_taxonomy WHERE taxonomy = 'category'";
				$catR = $wpdb->get_results($catQ, ARRAY_A);
				if ($catR) {
					foreach ($catR as $cat) {
						$catA[] = $cat['term_id'];
						
					}
					$categories = implode(',',$catA);
				}
			} else {
				if ($inc_child) {
					
					global $wpdb, $childrenA;
					$catA = explode(',',$categories);
					$categoriesA = $catA;
					foreach($catA as $check_cat) {
						$childrenA = array();
						$childrenA = build_children($check_cat);
						$categoriesA = array_merge($categoriesA,$childrenA);			
					}
					$categories = implode(',',$categoriesA);
				}
			}
	
			echo $before_widget;
			if ( $title )
				echo $before_title . $title . $after_title;
			echo get_calendar_custom($categories);
			echo $after_widget;
		}
		// end widget()
		
		/**
		 * Update - Widget_TC_Custom_Calendar
		 */
		function update( $new_instance, $old_instance )
		{
			$instance = $old_instance;
			$new_instance = wp_parse_args( (array) $new_instance, array( 'title' => '', 'categories' => '', 'inc_child' => 0 ) );
			
			$instance['title'] = strip_tags($new_instance['title']);
			$instance['categories'] = strip_tags($new_instance['categories']);
			
			return $instance;
		}
		// end update()
		
		/**
		 * Form - Widget_TC_Custom_Calendar
		 */
		function form( $instance )
		{
			$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'categories' => '', 'inc_child' => 1 ) );
			$title = strip_tags($instance['title']);
			$categories = strip_tags($instance['categories']);
			$inc_child = $instance['inc_child'] ? 'checked="checked"' : '';
			?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
			<p>
				<label for="<?php echo $this->get_field_id('categories'); ?>"><?php _e('Categories:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('categories'); ?>" name="<?php echo $this->get_field_name('categories'); ?>" type="text" value="<?php echo esc_attr($categories); ?>" />
				<br/>
				<small><?php _e('Category IDs, seperated by commas.');?></small>
			</p>
			<p>
				<input class="checkbox" type="checkbox" <?php echo $inc_child; ?> id="<?php echo $this->get_field_id('inc_child'); ?>" name="<?php echo $this->get_field_name('inc_child'); ?>" /> <label for="<?php echo $this->get_field_id('inc_child'); ?>"><?php _e('Include children'); ?></label>
			</p>
			<?php
		}
		// end form()
	}
	// end class Widget_TC_Custom_Calendar
	
	
	
	
	/**
	 * Sidebars
	 */
	if ( function_exists('register_sidebars') )
	{
		register_sidebar(array(
			'name' => 'Default Sidebar',
			'before_widget' => "\n<!-- default_widget start: %1\$s -->\n".'<div id="%1$s" class="default_widget widget box %2$s">',
			'after_widget' => "</div></div>\n<!-- default_widget end -->\n",
			'before_title' => "\n\t<h3>",
			'after_title' => "</h3><div class=\"inner\">\n"
		));
		register_sidebar(array(
			'name' => 'Default Post Sidebar',
			'before_widget' => "\n<!-- post_widget start: %1\$s -->\n".'<div id="%1$s" class="post_widget widget box %2$s">',
			'after_widget' => "</div></div>\n<!-- post_widget end -->\n",
			'before_title' => "\n\t<h3>",
			'after_title' => "</h3><div class=\"inner\">\n"
		));
		register_sidebar(array(
			'name' => 'Default Category Sidebar',
			'before_widget' => "\n<!-- category_widget start: %1\$s -->\n".'<div id="%1$s" class="category_widget widget box %2$s">',
			'after_widget' => "</div></div>\n<!-- category_widget end -->\n",
			'before_title' => "\n\t<h3>",
			'after_title' => "</h3><div class=\"inner\">\n"
		));
		register_sidebar(array(
			'name' => 'Default Page Sidebar',
			'before_widget' => "\n<!-- page_widget start: %1\$s -->\n".'<div id="%1$s" class="page_widget widget box %2$s">',
			'after_widget' => "</div></div>\n<!-- page_widget end -->\n",
			'before_title' => "\n\t<h3>",
			'after_title' => "</h3><div class=\"inner\">\n"
		));
	}
	add_action('widgets_init', 'tc_widgets_init', 1);
	
?>