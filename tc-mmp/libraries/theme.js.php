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
	 * ADD BROWSER SPECIFIC CLASS TO THE BODY
	 */
	function tc_js_browser_body_class( $classes ) {
		global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;

		if ( $is_lynx ) $classes[] = 'lynx';
		elseif ( $is_gecko ) $classes[] = 'gecko';
		elseif ( $is_opera ) $classes[] = 'opera';
		elseif ( $is_NS4 ) $classes[] = 'ns4';
		elseif ( $is_safari ) $classes[] = 'safari';
		elseif ( $is_chrome ) $classes[] = 'chrome';
		elseif ( $is_IE ) $classes[] = 'ie';
		else $classes[] = 'unknown';

		if ( $is_iphone ) $classes[] = 'iphone';
		return $classes;
	}

	/**
	 * EXPORT PHP ARRAY IN JAVASCRIPT NOTATION W/ TC LIB
	 */
	function tc_js_vars( $arr )
	{
		if ( !is_array( $arr ) || empty( $arr ) )
			return false;

		$out = array();
		foreach ( $arr as $k => $v )
		{
			is_array( $v ) || is_object( $v ) and $v = parse_js_arg($v);
			is_bool( $v ) and $v = ( $v ) ? 'true' : 'false';
			$out[] = empty( $k )
						 ? "tc_var.add('core', '$v');"
						 : "tc_var.add('$k', '$v');";
		}
		return empty( $out ) ? '' : ' ' . implode( ' ', $out);
	}
	function parse_js_arg($args)
	{
		$out = $tag = array();
		if ( is_array( $args ) )
		{
			$i = 0;
			foreach ( $args as $k => $v )
			{
				switch ( $v )
				{
					case is_array( $v ) || is_object( $v ):
						$v = parse_js_arg($v);
						break;
					case is_string( $v ):
						$v = "'$v'";
						break;
					case is_bool( $v ):
						$v = ($v) ? 'true' : 'false';
				}

				if ( empty( $k ) && !empty( $v ) ):
					$tag[] = "t_$i:$v";
					$i++;
				else:
					$out[] = "$k:$v";
				endif;
			}
		}
		$output = empty($tag)
						? '{' . implode( ',', $out) . '}'
						: '{' . implode( ',', $tag) . ',' . implode( ',', $out) . '}' ;

		return $output;
	}
?>