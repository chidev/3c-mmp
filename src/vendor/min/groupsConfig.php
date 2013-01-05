<?php
/**
 * Groups configuration for default Minify implementation
 * @package Minify
 */

/** 
 * You may wish to use the Minify URI Builder app to suggest
 * changes. http://yourdomain/min/builder/
 **/

$parts = explode('wp-content', $_SERVER['REQUEST_URI']);
$last = explode('/lib/min/', $parts[1]);
$base = '/' . rtrim($parts[0], '/');
$tpl = $base . '/wp-content' . $last[0];
$inc = $base . '/wp-includes';

return array(
	
	'js-base'		=> array
	(
		$tpl.'/js/jquery.treeview.async.js',
		$tpl.'/js/jquery.cookie.js',
		$tpl.'/js/jquery.treeview.pack.js',
		$tpl.'/js/gears_init.js',
		//$tpl.'/js/jquery.jcarousel.pack.js',
		//$tpl.'/js/superfish.js',
		//$tpl.'/js/jquery.bgiframe.min.js',
		//$tpl.'/js/hoverIntent.js',
		//$tpl.'/js/swfobject.js',
		$tpl.'/js/tc.common.js',
		//$tpl.'/js/tc.form.validation.js',
		$tpl.'/js/tc.pages.js'
	),
	
	'css-base'	=> array
	(
		$tpl.'/css/plugins.weather.css', 
		$tpl.'/css/plugins.slider.css', 
		$tpl.'/css/tc.reset.css', 
		$tpl.'/css/960.css', 
		$tpl.'/purlagent/css/pa.styles.css',
		$tpl.'/purlagent/css/pa.blocks.css',
		$tpl.'/purlagent/css/pa.sprite.ico.css',
		$tpl.'/css/tc.sprite.ico.css',
		$tpl.'/css/tc.type.css',
		$tpl.'/css/tc.form.css',
		$tpl.'/css/tc.table.css',
		$tpl.'/css/tc.helpers.css',
		$tpl.'/css/tc.blocks.css',
		$tpl.'/css/tc.styles.css', 
		$tpl.'/css/tc.pages.css'
	)
);