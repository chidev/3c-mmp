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
	 * CUSTOM CALENDAR // EXCLUDE CATEGORIES
	 */
	function get_calendar_custom($catid,$initial = true) {
		global $wpdb, $m, $monthnum, $year, $wp_locale, $posts;
		
	
		$key = md5( $m . $monthnum . $year );
		if ( $cache = wp_cache_get( 'get_calendar_custom', 'calendar_custom' ) ) {
			if ( isset( $cache[ $key ] ) ) {
				echo $cache[ $key ];
				return;
			}
		}
	
	
		ob_start();
		// Quick check. If we have no posts at all, abort!
		if ( !$posts ) {
			$gotsome = $wpdb->get_var("SELECT ID from $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish' ORDER BY post_date DESC LIMIT 1");
			if ( !$gotsome )
				return;
		}
	
		if ( isset($_GET['w']) )
			$w = ''.intval($_GET['w']);
	
		// week_begins = 0 stands for Sunday
		$week_begins = intval(get_option('start_of_week'));
	
		// Let's figure out when we are
		if ( !empty($monthnum) && !empty($year) ) {
			$thismonth = ''.zeroise(intval($monthnum), 2);
			$thisyear = ''.intval($year);
		} elseif ( !empty($w) ) {
			// We need to get the month from MySQL
			$thisyear = ''.intval(substr($m, 0, 4));
			$d = (($w - 1) * 7) + 6; //it seems MySQL's weeks disagree with PHP's
			$thismonth = $wpdb->get_var("SELECT DATE_FORMAT((DATE_ADD('${thisyear}0101', INTERVAL $d DAY) ), '%m')");
		} elseif ( !empty($m) ) {
			$thisyear = ''.intval(substr($m, 0, 4));
			if ( strlen($m) < 6 )
					$thismonth = '01';
			else
					$thismonth = ''.zeroise(intval(substr($m, 4, 2)), 2);
		} else {
			$thisyear = gmdate('Y', current_time('timestamp'));
			$thismonth = gmdate('m', current_time('timestamp'));
		}
	
		$unixmonth = mktime(0, 0 , 0, $thismonth, 1, $thisyear);
	
		// Get the next and previous month and year with at least one post
		$previous = $wpdb->get_row("SELECT DISTINCT MONTH(post_date) AS month, YEAR(post_date) AS year
			FROM $wpdb->posts
			LEFT JOIN $wpdb->term_relationships ON($wpdb->posts.ID = $wpdb->term_relationships.object_id) 
			LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id) 
			
			WHERE post_date < '$thisyear-$thismonth-01'
	
			AND $wpdb->term_taxonomy.term_id IN ($catid) 
			AND $wpdb->term_taxonomy.taxonomy = 'category' 
	
			AND post_type = 'post' AND post_status = 'publish'
				ORDER BY post_date DESC
				LIMIT 1");
	
		$next = $wpdb->get_row("SELECT	DISTINCT MONTH(post_date) AS month, YEAR(post_date) AS year
			FROM $wpdb->posts
			LEFT JOIN $wpdb->term_relationships ON($wpdb->posts.ID = $wpdb->term_relationships.object_id) 
			LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
			
			WHERE post_date >	'$thisyear-$thismonth-01'
	
			AND $wpdb->term_taxonomy.term_id IN ($catid) 
			AND $wpdb->term_taxonomy.taxonomy = 'category' 
	
			AND MONTH( post_date ) != MONTH( '$thisyear-$thismonth-01' )
			AND post_type = 'post' AND post_status = 'publish'
				ORDER	BY post_date ASC
				LIMIT 1");
	
		echo '<div id="calendar_wrap">
		<table id="wp-calendar" summary="' . __('Calendar') . '">
		<caption>' . sprintf(_c('%1$s %2$s|Used as a calendar caption'), $wp_locale->get_month($thismonth), date('Y', $unixmonth)) . '</caption>
		<thead>
		<tr>';
	
		$myweek = array();
	
		for ( $wdcount=0; $wdcount<=6; $wdcount++ ) {
			$myweek[] = $wp_locale->get_weekday(($wdcount+$week_begins)%7);
		}
	
		foreach ( $myweek as $wd ) {
			$day_name = (true == $initial) ? $wp_locale->get_weekday_initial($wd) : $wp_locale->get_weekday_abbrev($wd);
			echo "\n\t\t<th abbr=\"$wd\" scope=\"col\" title=\"$wd\">$day_name</th>";
		}
	
		echo '
		</tr>
		</thead>
	
		<tfoot>
		<tr>';
	
		if ( $previous ) {
			echo "\n\t\t".'<td abbr="' . $wp_locale->get_month($previous->month) . '" colspan="3" id="prev"><a href="' .
			get_month_link($previous->year, $previous->month) . '?catid='.$catid.'" title="' . sprintf(__('View posts for %1$s %2$s'), $wp_locale->get_month($previous->month),
				date('Y', mktime(0, 0 , 0, $previous->month, 1, $previous->year))) . '">&laquo; ' . $wp_locale->get_month_abbrev($wp_locale->get_month($previous->month)) . '</a></td>';
		} else {
			echo "\n\t\t".'<td colspan="3" id="prev" class="pad">&nbsp;</td>';
		}
	
		echo "\n\t\t".'<td class="pad">&nbsp;</td>';
	
		if ( $next ) {
			echo "\n\t\t".'<td abbr="' . $wp_locale->get_month($next->month) . '" colspan="3" id="next"><a href="' .
			get_month_link($next->year, $next->month) . '?catid='.$catid.'" title="' . sprintf(__('View posts for %1$s %2$s'), $wp_locale->get_month($next->month),
				date('Y', mktime(0, 0 , 0, $next->month, 1, $next->year))) . '">' . $wp_locale->get_month_abbrev($wp_locale->get_month($next->month)) . ' &raquo;</a></td>';
		} else {
			echo "\n\t\t".'<td colspan="3" id="next" class="pad">&nbsp;</td>';
		}
	
		echo '
		</tr>
		</tfoot>
	
		<tbody>
		<tr>';
	
		// Get days with posts
		$dyp_sql = "SELECT DISTINCT DAYOFMONTH(post_date)
			FROM $wpdb->posts 
	
			LEFT JOIN $wpdb->term_relationships ON($wpdb->posts.ID = $wpdb->term_relationships.object_id) 
			LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id) 
			
			WHERE MONTH(post_date) = '$thismonth' 
	
			AND $wpdb->term_taxonomy.term_id IN ($catid) 
			AND $wpdb->term_taxonomy.taxonomy = 'category' 
	
			AND YEAR(post_date) = '$thisyear' 
			AND post_type = 'post' AND post_status = 'publish' 
			AND post_date < '" . current_time('mysql') . "'";
			
		$dayswithposts = $wpdb->get_results($dyp_sql, ARRAY_N);
	
		if ( $dayswithposts ) {
			foreach ( (array) $dayswithposts as $daywith ) {
				$daywithpost[] = $daywith[0];
			}
		} else {
			$daywithpost = array();
		}
	
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false || strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'camino') !== false || strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'safari') !== false)
			$ak_title_separator = "\n";
		else
			$ak_title_separator = ', ';
	
		$ak_titles_for_day = array();
		$ak_post_titles = $wpdb->get_results("SELECT post_title, DAYOFMONTH(post_date) as dom "
			."FROM $wpdb->posts "
			
			."LEFT JOIN $wpdb->term_relationships ON($wpdb->posts.ID = $wpdb->term_relationships.object_id) "
			."LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id) "
			
			."WHERE YEAR(post_date) = '$thisyear' "
			
			."AND $wpdb->term_taxonomy.term_id IN ($catid) "
			."AND $wpdb->term_taxonomy.taxonomy = 'category' "
	
			."AND MONTH(post_date) = '$thismonth' "
			."AND post_date < '".current_time('mysql')."' "
			."AND post_type = 'post' AND post_status = 'publish'"
		);
		if ( $ak_post_titles ) {
			foreach ( (array) $ak_post_titles as $ak_post_title ) {
	
					$post_title = apply_filters( "the_title", $ak_post_title->post_title );
					$post_title = str_replace('"', '&quot;', wptexturize( $post_title ));
	
					if ( empty($ak_titles_for_day['day_'.$ak_post_title->dom]) )
						$ak_titles_for_day['day_'.$ak_post_title->dom] = '';
					if ( empty($ak_titles_for_day["$ak_post_title->dom"]) ) // first one
						$ak_titles_for_day["$ak_post_title->dom"] = $post_title;
					else
						$ak_titles_for_day["$ak_post_title->dom"] .= $ak_title_separator . $post_title;
			}
		}
	
	
		// See how much we should pad in the beginning
		$pad = calendar_week_mod(date('w', $unixmonth)-$week_begins);
		if ( 0 != $pad )
			echo "\n\t\t".'<td colspan="'.$pad.'" class="pad">&nbsp;</td>';
	
		$daysinmonth = intval(date('t', $unixmonth));
		for ( $day = 1; $day <= $daysinmonth; ++$day ) {
			if ( isset($newrow) && $newrow )
				echo "\n\t</tr>\n\t<tr>\n\t\t";
			$newrow = false;
	
			if ( $day == gmdate('j', (time() + (get_option('gmt_offset') * 3600))) && $thismonth == gmdate('m', time()+(get_option('gmt_offset') * 3600)) && $thisyear == gmdate('Y', time()+(get_option('gmt_offset') * 3600)) )
				echo '<td id="today">';
			else
				echo '<td>';
	
			if ( in_array($day, $daywithpost) ) // any posts today?
					echo '<a href="' . get_day_link($thisyear, $thismonth, $day) . "?catid=$catid\" title=\"$ak_titles_for_day[$day]\">$day</a>";
			else
				echo $day;
			echo '</td>';
	
			if ( 6 == calendar_week_mod(date('w', mktime(0, 0 , 0, $thismonth, $day, $thisyear))-$week_begins) )
				$newrow = true;
		}
	
		$pad = 7 - calendar_week_mod(date('w', mktime(0, 0 , 0, $thismonth, $day, $thisyear))-$week_begins);
		if ( $pad != 0 && $pad != 7 )
			echo "\n\t\t".'<td class="pad" colspan="'.$pad.'">&nbsp;</td>';
	
		echo "\n\t</tr>\n\t</tbody>\n\t</table></div>";
	
		$output = ob_get_contents();
		ob_end_clean();
		echo $output;
		$cache[ $key ] = $output;
		wp_cache_set( 'get_calendar_custom', $cache, 'calendar_custom' );
	}
	
	function get_archives_custom($args = '') {
		global $wpdb, $wp_locale;
	
		$defaults = array(
			'type' => 'monthly', 'limit' => '',
			'format' => 'html', 'before' => '',
			'after' => '', 'show_post_count' => false,
			'echo' => 1,
			'cat' => '7'
		);
		
		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );
	
		if ( '' == $type )
			$type = 'monthly';
	
		if ( '' != $limit ) {
			$limit = absint($limit);
			$limit = ' LIMIT '.$limit;
		}
	
		// this is what will separate dates on weekly archive links
		$archive_week_separator = '&#8211;';
	
		// over-ride general date format ? 0 = no: use the date format set in Options, 1 = yes: over-ride
		$archive_date_format_over_ride = 0;
	
		// options for daily archive (only if you over-ride the general date format)
		$archive_day_date_format = 'Y/m/d';
	
		// options for weekly archive (only if you over-ride the general date format)
		$archive_week_start_date_format = 'Y/m/d';
		$archive_week_end_date_format	= 'Y/m/d';
	
		if ( !$archive_date_format_over_ride ) {
			$archive_day_date_format = get_option('date_format');
			$archive_week_start_date_format = get_option('date_format');
			$archive_week_end_date_format = get_option('date_format');
		}
	
		//filters
		$catid = $r['cat'];
	
		$where = apply_filters('getarchives_where', "WHERE post_type = 'post' AND post_status = 'publish'", $r );
		$join = apply_filters('getarchives_join', "", $r);
		$join .= " 
			LEFT JOIN $wpdb->term_relationships ON($wpdb->posts.ID = $wpdb->term_relationships.object_id) 
			LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)";
			
		$where .= " 
			AND $wpdb->term_taxonomy.term_id IN ($catid) 
			AND $wpdb->term_taxonomy.taxonomy = 'category'";
		
		
		$output = '';
	
		if ( 'monthly' == $type ) {
			$query = "SELECT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, count(DISTINCT ID) as posts FROM $wpdb->posts $join $where GROUP BY YEAR(post_date), MONTH(post_date) ORDER BY post_date DESC $limit";
			$key = md5($query);
			$cache = wp_cache_get( 'wp_get_archives' , 'general');
			if ( !isset( $cache[ $key ] ) ) {
				$arcresults = $wpdb->get_results($query);
				$cache[ $key ] = $arcresults;
				wp_cache_add( 'wp_get_archives', $cache, 'general' );
			} else {
				$arcresults = $cache[ $key ];
			}
			if ( $arcresults ) {
				$afterafter = $after;
				foreach ( (array) $arcresults as $arcresult ) {
					$url = get_month_link( $arcresult->year, $arcresult->month ) . "?catid=$catid";
					$text = sprintf(__('%1$s %2$d'), $wp_locale->get_month($arcresult->month), $arcresult->year);
					if ( $show_post_count )
						$after = '&nbsp;('.$arcresult->posts.')' . $afterafter;
					$output .= get_archives_link($url, $text, $format, $before, $after);
				}
			}
		} elseif ('yearly' == $type) {
			$query = "SELECT YEAR(post_date) AS `year`, count(DISTINCT ID) as posts FROM $wpdb->posts $join $where GROUP BY YEAR(post_date) ORDER BY post_date DESC $limit";
			$key = md5($query);
			$cache = wp_cache_get( 'wp_get_archives' , 'general');
			if ( !isset( $cache[ $key ] ) ) {
				$arcresults = $wpdb->get_results($query);
				$cache[ $key ] = $arcresults;
				wp_cache_add( 'wp_get_archives', $cache, 'general' );
			} else {
				$arcresults = $cache[ $key ];
			}
			if ($arcresults) {
				$afterafter = $after;
				foreach ( (array) $arcresults as $arcresult) {
					$url = get_year_link($arcresult->year) . "?catid=$catid";
					$text = sprintf('%d', $arcresult->year);
					if ($show_post_count)
						$after = '&nbsp;('.$arcresult->posts.')' . $afterafter;
					$output .= get_archives_link($url, $text, $format, $before, $after);
				}
			}
		} elseif ( 'daily' == $type ) {
			$query = "SELECT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, DAYOFMONTH(post_date) AS `dayofmonth`, count(DISTINCT ID) as posts FROM $wpdb->posts $join $where GROUP BY YEAR(post_date), MONTH(post_date), DAYOFMONTH(post_date) ORDER BY post_date DESC $limit";
			$key = md5($query);
			$cache = wp_cache_get( 'wp_get_archives' , 'general');
			if ( !isset( $cache[ $key ] ) ) {
				$arcresults = $wpdb->get_results($query);
				$cache[ $key ] = $arcresults;
				wp_cache_add( 'wp_get_archives', $cache, 'general' );
			} else {
				$arcresults = $cache[ $key ];
			}
			if ( $arcresults ) {
				$afterafter = $after;
				foreach ( (array) $arcresults as $arcresult ) {
					$url	= get_day_link($arcresult->year, $arcresult->month, $arcresult->dayofmonth) . "?catid=$catid";
					$date = sprintf('%1$d-%2$02d-%3$02d 00:00:00', $arcresult->year, $arcresult->month, $arcresult->dayofmonth);
					$text = mysql2date($archive_day_date_format, $date);
					if ($show_post_count)
						$after = '&nbsp;('.$arcresult->posts.')'.$afterafter;
					$output .= get_archives_link($url, $text, $format, $before, $after);
				}
			}
		} elseif ( 'weekly' == $type ) {
			$start_of_week = get_option('start_of_week');
			$query = "SELECT WEEK(post_date, $start_of_week) AS `week`, YEAR(post_date) AS yr, DATE_FORMAT(post_date, '%Y-%m-%d') AS yyyymmdd, count(DISTINCT ID) as posts FROM $wpdb->posts $join $where GROUP BY WEEK(post_date, $start_of_week), YEAR(post_date) ORDER BY post_date DESC $limit";
			$key = md5($query);
			$cache = wp_cache_get( 'wp_get_archives' , 'general');
			if ( !isset( $cache[ $key ] ) ) {
				$arcresults = $wpdb->get_results($query);
				$cache[ $key ] = $arcresults;
				wp_cache_add( 'wp_get_archives', $cache, 'general' );
			} else {
				$arcresults = $cache[ $key ];
			}
			$arc_w_last = '';
			$afterafter = $after;
			if ( $arcresults ) {
					foreach ( (array) $arcresults as $arcresult ) {
						if ( $arcresult->week != $arc_w_last ) {
							$arc_year = $arcresult->yr;
							$arc_w_last = $arcresult->week;
							$arc_week = get_weekstartend($arcresult->yyyymmdd, get_option('start_of_week'));
							$arc_week_start = date_i18n($archive_week_start_date_format, $arc_week['start']);
							$arc_week_end = date_i18n($archive_week_end_date_format, $arc_week['end']);
							$url  = sprintf('%1$s/%2$s%3$sm%4$s%5$s%6$sw%7$s%8$d', get_option('home'), '', '?', '=', $arc_year, '&amp;', '=', $arcresult->week) . "?catid=$catid";
							$text = $arc_week_start . $archive_week_separator . $arc_week_end;
							if ($show_post_count)
								$after = '&nbsp;('.$arcresult->posts.')'.$afterafter;
							$output .= get_archives_link($url, $text, $format, $before, $after);
						}
					}
			}
		} elseif ( ( 'postbypost' == $type ) || ('alpha' == $type) ) {
			$orderby = ('alpha' == $type) ? "post_title ASC " : "post_date DESC ";
			$query = "SELECT DISTINCT * FROM $wpdb->posts $join $where GROUP BY ID ORDER BY $orderby $limit";
			$key = md5($query);
			$cache = wp_cache_get( 'wp_get_archives' , 'general');
			if ( !isset( $cache[ $key ] ) ) {
				$arcresults = $wpdb->get_results($query);
				$cache[ $key ] = $arcresults;
				wp_cache_add( 'wp_get_archives', $cache, 'general' );
			} else {
				$arcresults = $cache[ $key ];
			}
			if ( $arcresults ) {
				foreach ( (array) $arcresults as $arcresult ) {
					if ( $arcresult->post_date != '0000-00-00 00:00:00' ) {
						$url  = get_permalink($arcresult);
						$arc_title = $arcresult->post_title;
						if ( $arc_title )
							$text = strip_tags(apply_filters('the_title', $arc_title));
						else
							$text = $arcresult->ID;
						$output .= get_archives_link($url, $text, $format, $before, $after);
					}
				}
			}
		}
		if ( $echo )
			echo $output;
		else
			return $output;
	}
	
?>