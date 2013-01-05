<?php
	//facebook application
	$fbconfig['appid']		= "140629805981221";
	$fbconfig['secret']		= "90ca64f8abfea09ffdf936b844fed1a6";
	$fbconfig['baseurl']	= "http://www.xennightz.com/?fbcb=owner"; //"http://thinkdiff.net/demo/newfbconnect1/php/sdk3/index.php";
	$fbconfig['uid']			= '509974945';
	$fbconfig['token']		= '140629805981221|1bd2728729a31b49dbc188ad.1-509974945|FtHA7uW4VFxeHO1f2aiuALe8S2M';
	
	$code = $_REQUEST["code"];
	
	$token_url = "https://graph.facebook.com/oauth/access_token?"
	. "client_id=" . $fbconfig['appid'] . "&redirect_uri=" . urlencode( $fbconfig['baseurl'] )
	. "&client_secret=" . $fbconfig['secret'] . "&code=" . $code;
	
	//$response = file_get_contents($token_url);
	//$params = null;
	//parse_str($response, $params);
	
	
	//$fbconfig['token'] = $params['access_token'];
	//echo $fbconfig['token'];
	
	$uid = $fbconfig['uid'];
	
	try
	{
		include_once "facebook.php";
	}
	catch(Exception $o){
		error_log($o);
	}
	// Create our Application instance.
	$facebook = new Facebook(array(
	'appId'  => $fbconfig['appid'],
	'secret' => $fbconfig['secret'],
	'cookie' => true,
	));
	
	try
	{
		$user = $facebook->api('/me', array('access_token'=>$fbconfig['token']));
	}
	catch(Exception $o){
		error_log($o);
	}
	
	$loginUrl   = $facebook->getLoginUrl(
	array(
	'scope'         => 'email,user_photo_video_tags,user_photos,user_videos,offline_access,publish_stream,user_birthday,user_location,user_work_history,user_about_me,user_hometown',
	'redirect_uri'  => $fbconfig['baseurl']
	)
	);
	
	$logoutUrl  = $facebook->getLogoutUrl();
	
	$albums = $photos = false;
	
	/*
	try{
		$albums = $facebook->api("/$uid/albums", array('access_token'=>$fbconfig['token']));
	
		foreach ($albums['data'] as $album)
		{
	
			$aid = $album['id'];
			$pix = $facebook->api("/$aid/photos", array('access_token'=>$fbconfig['token']));
			foreach ($pix['data'] as $pic)
			{
				$pid = $pic['id'];
				$photos[$aid][$pid] = $pic;
			}
		}
	}
	catch(Exception $o){
		d($o);
	}
	*/
	try{
		$tagged = $facebook->api("/$uid/photos?limit=200", array('access_token'=>$fbconfig['token']));
	}
	catch(Exception $o){
		d($o);
	}
	
	/*
	//update user's status using graph api
	//http://developers.facebook.com/docs/reference/dialogs/feed/
	if (isset($_GET['publish'])){
	try {
	$publishStream = $facebook->api("/$user/feed", 'post', array(
	'message' => "I love thinkdiff.net for facebook app development tutorials. :)",
	'link'    => 'http://ithinkdiff.net',
	'picture' => 'http://thinkdiff.net/ithinkdiff.png',
	'name'    => 'iOS Apps & Games',
	'description'=> 'Checkout iOS apps and games from iThinkdiff.net. I found some of them are just awesome!'
	)
	);
	//as $_GET['publish'] is set so remove it by redirecting user to the base url
	} catch (FacebookApiException $e) {
	d($e);
	}
	$redirectUrl     = $fbconfig['baseurl'] . '/index.php?success=1';
	header("Location: $redirectUrl");
	}
	
	//update user's status using graph api
	//http://developers.facebook.com/docs/reference/dialogs/feed/
	if (isset($_POST['tt'])){
	try {
	$statusUpdate = $facebook->api("/$user/feed", 'post', array('message'=> $_POST['tt']));
	} catch (FacebookApiException $e) {
	d($e);
	}
	}
	*/
	
	function d($d){
		echo '<pre>';
		print_r($d);
		echo '</pre>';
	}
?>
