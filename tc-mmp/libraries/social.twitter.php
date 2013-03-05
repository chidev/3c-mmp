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
	
	class TC_Social
	{
		public function __construct()
		{
			
		}
		public function open_driver()
		{
		}
		public function close_driver()
		{
		}
	}
	
	class TC_Twitter extends TC_Social
	{
		public function __construct()
		{
			
		}
	}
	
	/**
	 * Retrieve recent tweets
	 */
	function tc_twitter_recent($args)
	{
		global $tc;
		$cache = $tc->cache;
		
		$default = array(
			'limit'									=> 5,
			'username'							=> 'graphicsms',
			'use_avatar'						=> true,
			'cache'									=> 180,
			'link_tweet'						=> false,
			'link_avatar'						=> true,
			'link_users'						=> true,
			'link_hashes'						=> 'twitter',
			'display_format'				=> 'ul'
		);
		$args = wp_parse_args( $args, $defaults );
		$args = apply_filters( 'tc_twitter_recent_args', $args );
		$args = (object) $args;
		
		$args->limit > 20 and $args->limit = 20;
		$args->limit < 5 and $args->limit = 5;
		
		$cache_id = array($args->limit, $args->username);
		$cache_id = implode('_', $cache_id);
		
		$do_cache = (int) $args->cache;
		if ( $do_cache === 0 || ( $tweet_data = TC::cache_get( $cache_id ) ) === false )
		{
			$json = @file_get_contents('http://twitter.com/status/user_timeline/'.$args->username.'.json?count=100');
			
			if ($json)
			{
				$tweet_data = json_decode($json, true);
			}
			else {
				$tweets = false;
			}
		}
		
		var_dump($do_cache, $tweet_data);
		
		TC::cache_set($cache_id, $tweet_data);
		
		// Setup Tweets
		for ($i=0; $i < $args->limit; $i++)
		{
			if (isset($tweets[$i]))
			{
				$tweets[] = array(
					'name'							=> $footer_tweets[$i]['user']['name'],
					'screen_name'				=> $footer_tweets[$i]['user']['screen_name'],
					'img'								=> $footer_tweets[$i]['user']['profile_image_url'],
					'tweet'							=> tc_make_clickable( $tweets[$i]['text'] )
				);
			}
		}
	}

?>