<?php defined('TC_SYSTEM_PATH') OR die('No direct access allowed.<br/>'.__FILE__);
/**
 * MMP - SHARE
 *
 * @version			0.0.1
 * @author			Jean-Patrick Smith
 * @copyright		2013 3rd Corner Studios
 * @url					http://www.3rdcornerstudios.com
 */

class TC_MMP_Share extends TC_Plugin
{
	/**
	 * @var config object
	 */
	public static $config;

	/**
	 * @var cache object
	 */
	public static $cache;

	/**
	 * @var loaded plugins array
	 */
	public static $sites;

	/**
	 * zxxxxxxxxxxx
	 */
	public function __construct( $load_fb )
	{
		global $tconf;

		if (isset($_GET['fbcb']))
		{
			include($tconf['fb_path'].'/index.php');
			die;
		}
		if (isset($_GET['fbphotos']))
		{
			include($tconf['fb_path'].'/inline.php');
			die;
		}
	}

	/**
	 * Generate share text
	 */
	public static function share_text( $song_info )
	{
		// share setup
		$share_html = array('Share');
		$sh_mp3_enc_url = urlencode( $track_mp3 );
		$sh_title = 'I like Xen Nightz: '.$song_info['title'];

		$mp3_url = home_url('/song/'.$song_info['md5']);
		// fb
		$sh_fb_title = substr( $sh_title, 0, 420 );
		if ( strlen( $sh_fb_title ) + strlen( $mp3_url ) > 410 )
		{
			$len = strlen( $mp3_url ) + 4;
			$left = 140 - $len;
			$sh_fb_title = substr( $sh_fb_title, 0, $left);
		}
		$sh_fb_txt = urlencode( $sh_fb_title );
		$sh_fb_url = 'http://www.facebook.com/sharer.php?u='.urlencode( $mp3_url ).'&amp;title='.$sh_fb_txt;
		$share_html[] = '<a rel="nofollow" href="'.$sh_fb_url.'" target="_blank" class="soc fb">Facebook</a>';
		//$share_html[] = '<a href="'.$track_mp3.'" class="soc fb"></a>';
		//$share_html[] = '<fb:like href="'.$track_mp3.'" layout="button_count" width="100" show_faces="false" colorscheme="dark" font="arial"></fb:like>';
		// twt

		// twt
		$sh_twt_title = substr( $sh_title, 0, 140 );
		if ( strlen( $sh_twt_title ) + strlen( $mp3_url ) > 136 )
		{
			$len = strlen( $mp3_url ) + 4;
			$left = 140 - $len;
			$sh_twt_title = substr( $sh_twt_title, 0, $left );
		}
		$sh_twt_txt = urlencode( $sh_twt_title.' &#9835; ' );

		//$sh_twt_url = 'http://platform.twitter.com/widgets/tweet_button.html?url='.urlencode( $mp3_url ).'&via=XenNightz&text='.$sh_twt_txt;
		//$sh_twt_url = 'http://platform.twitter.com/widgets/tweet_button.html?url='.urlencode( $mp3_url ).'&via=XenNightz&text='.$sh_twt_txt;

		$share_html[] = '<a target="_blank" rel="nofollow" href="http://twitter.com/share?url='.urlencode( $mp3_url ).'&amp;via=xennightz&amp;text='.$sh_twt_txt.'" class="soc twt">Tweet</a>';
		//$share_html[] = '<a target="_blank" rel="nofollow" href="http://twitter.com/share?url='.urlencode( 'http://www.xennightz.com' ).'&amp;via=xennightz&amp;text='.$sh_twt_txt.'" class="soc twt">Tweet</a>';
		//$share_html[] = '<a target="_blank" rel="nofollow" href="http://twitter.com/share?url='.$sh_mp3_enc_url.'&amp;via=xennightz&amp;text='.$sh_twt_txt.'" class="twitter-share-button">Tweet</a>';
		//$share_html[] = '<a href="'.$sh_twt_url.'" target="_blank" class="twitter-share-button">Tweet</a>';
		//$share_html[] = '<a href="'.$sh_twt_url.'" class="soc twt"></a>';
		// '<iframe allowtransparency="true" frameborder="0" scrolling="no" src='.$url.' style="width:130px; height:50px;"></iframe>';
		// embed
		// ...

		$share = implode( ' | ', $share_html );
		$share .= '<div style="display:none;">'.$mp3_url.'</div>';

		return $share;
	}
}
?>