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

define('ID3LIBPATH', EXTERNAL_LIBPATH.'/id3_lib/getid3/getid3.php');

$config['audio_path'] = ABSPATH.'audio';
$config['audio_url'] = get_bloginfo('url').'/audio';

$config['cached_path'] = EXTERNAL_LIBPATH.'/id3_lib/cache/cached.albums.data';

$config['lyrics_path_mask'] = 'lyrics';