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
	
	include(TC_GLOBALS);
	
	function tc_mp3_redirect()
	{
		$mmp = new TC_MMP('mmp');
		
		$file = explode( '/', $_GET['mp3'], 2 );
	
		/**
		 * DEBUG
		 *
		echo '<br/><br/><div style="display:block;background:#f0f0f0;border:2px solid #ccc;margin:20px;padding:15px;color:#333;"><pre>';
		var_dump( $file[1], preg_match( '/^.*\.mp3$/i', $file[1] ) );
		echo '</pre></div><hr/><br/><br/><br/><br/>';
		die;
		/**/
		
		if ( preg_match('/^.*\.mp3$/i', $file[1] ) > 0 )
		{
			$serve_mp3_url = $mmp->audio_url .'/'. $_GET['mp3'];
			$serve_mp3_path = $mmp->audio_path .'/'. $_GET['mp3'];
			$serve_mp3_filename = explode( $serve_mp3_path, '/', 2 );
		
			if ( file_exists( $serve_mp3_path ) )
			{
				//ECHO filesize( $serve_mp3_path ); DIE;
				
				/**/
				
				header( 'Content-Type: audio/mpeg, audio/x-mpeg, audio/x-mpeg-3, audio/mpeg3' );
		    //header( 'Content-Type: application/octet-stream' );
		    
		    //header( 'Content-Description: File Transfer' );
				
				header( 'Content-Length: ' . filesize( $serve_mp3_path ) );
				header( 'Content-Transfer-Encoding: binary' ); 
				
				header( 'Content-Disposition: filename="' . basename( $serve_mp3_path ) );
		    //header( 'Content-Disposition: attachment; filename='.basename( $serve_mp3_path ) );
		    
		    //header( 'Pragma: public' );
				//header( 'X-Pad: avoid browser bug' );
		    header( 'Expires: 0' );
		    header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
				ob_clean();
		    flush();
		    /**/
		    
	    	readfile( $serve_mp3_path );
	    	
				die;
			}
		}
		
		header( $_SERVER['SERVER_PROTOCOL'].' 404 Not Found', true, 404 );
		echo 'no file';
		die;
		
	}
	if ( isset( $_GET['mp3'] ) )
	{
		add_action( 'init', 'tc_mp3_redirect', 200 );
	}
	
?>