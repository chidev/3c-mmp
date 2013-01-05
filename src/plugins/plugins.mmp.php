<?php defined('TC_SYSTEM_PATH') OR die('No direct access allowed.<br/>'.__FILE__);
/**
 * MMP
 *
 * @version			0.0.1
 * @author			Jean-Patrick Smith
 * @copyright		2013 3rd Corner Studios
 * @url					http://www.3rdcornerstudios.com
 */

	class TC_MMP extends TC_Plugin
	{
		public $class_name;

		private $_clean = array();
		private $_errors = array();

		private $_status;

		private $_id;

		private $refresh_cache;

		private $do_debug;

		/**
		 * Libraries
		 */
		private $getID3;
		private $zip;

		/**
		 * Config values will be autoloaded at runtime
		 */
		public $cached_path;
		public $audio_url;
		public $audio_path;
		public $lyrics_path_mask;

		/**
		 * These values will be populate by the class containing information about
		 * albums, tracks, and covers etc
		 */
		public $album_paths = array();
		public $album_cover_url = 'http://www.xennightz.com/splash.jpg';
		public $lyrics;

		public $song_index;
		public $song_time_index;
		public $song_md5_index;

		// social
		public $social;

		/**
		 * These values need to be loaded from artist's specific settings
		 */
		public $fallback_artist;
		public $fallback_genre;

		/**
		 * This array will contain the status of whethers or not a song has lyrics
		 * Deprecated
		 */
		public $lyrics_status = array();

		/**
		 * Login/user variables
		 */
		public $is_login;

		/**
		 * Used by the class for iteration
		 */
		private $curr_mp3_data;
		private $curr_lyrics_path;
		private $curr_album_has_lyrics;

		/**
		 * CONSTRUCT CLASS
		 */
		public function __construct($id = null)
		{
			$this->class_name = get_class($this);
			//($id === null) and $this->_id = mt_rand();
			parent::__construct();

			// LOAD CONFIG
			$mmp_conf = TC::config('mmp');

			$this->audio_path = rtrim( $mmp_conf['audio_path'], '/' );
			$this->audio_url = rtrim( $mmp_conf['audio_url'], '/' );
			$this->cached_path = $mmp_conf['cached_path'];
			$this->lyrics_path_mask = $mmp_conf['lyrics_path_mask'];

			/**
			 * SOCIAL
			 */
			add_action( 'get_footer', array( $this, 'shutdown' ) );

			/**
			 * GET LYRICS
			 */
			if (isset($_GET['get_lyrics']) && $_GET['get_lyrics'] == 'popup')
			{
				$album_id = (isset($_GET['album_id']))
									? (int) $_GET['album_id']
									: 0;

				$track_id = (isset($_GET['track_id']))
									? (int) $_GET['track_id']
									: 0;

				if ($album_id > 0 && $track_id > 0)
					$this->get_lyrics($album_id, $track_id);
			}

			/**
			 * LOGIN
			 */
			if (isset($_GET['fbcb']) && $_GET['fbcb'] == 'owner')
			{

			}

			// SET DEBUG FLAG
			$this->do_debug = isset($_GET['do_debug']);

			$this->refresh_cache = FALSE;

			// CHECK FOR CACHED FILE
			if ( ! file_exists($this->cached_path) || do_cache() )
			{
				$this->refresh_cache = TRUE;
			}

			elseif ( ! $this->refresh_cache && file_exists($this->cached_path))
			{
				try
				{
					$get_album_data = file_get_contents($this->cached_path);
					$this->data = unserialize($get_album_data);
					$this->album_paths = $this->data['album_paths'];
					$this->song_index = $this->data['song_index'];
					$this->song_time_index = $this->data['song_time_index'];
					$this->song_md5_index = $this->data['song_md5_index'];
				}
				catch(Exception $e)
				{
				}

				if (empty($this->album_paths))
				{
					$this->refresh_cache = TRUE;
				}
			}

			/**
			 * ALLOW OVERRIDES FROM GET INPUT
			 */
			do_cache() and $this->refresh_cache = TRUE;

			if ( $this->do_debug )
			{
				$mtime = microtime();
				$mtime = explode(" ",$mtime);
				$mtime = $mtime[1] + $mtime[0];
				$starttime = $mtime;
			}

			if( $this->refresh_cache ) {
				set_time_limit(300);
				$this->process_albums();
			}

			if ( $this->do_debug )
			{
				echo '<div style="padding:20px; background:#efefef;font-size:16px;color:#333;text-align:left;"><pre>';

				$mtime = microtime();
				$mtime = explode(" ",$mtime);
				$mtime = $mtime[1] + $mtime[0];
				$endtime = $mtime;
				$totaltime = ($endtime - $starttime);

				if ($this->refresh_cache)
				{
					echo "All mp3 files were cached in ".$totaltime." seconds";
				}
				else {
					echo "All mp3 files were loaded from the cache in ".$totaltime." seconds";
				}

				echo '<hr/>';

				$latest_index = array_keys( $this->song_time_index );
				$latest_mp3 = $this->song_time_index[ array_pop( $latest_index ) ];

				var_dump($latest_mp3);

				echo '<hr/>';

				print_r($this->album_paths);

				echo '</pre></div>';
				die;
			}

		} // construct

		/**
		 *  REFRESH ALBUM CACHE
		 */
		public function process_albums()
		{
			require_once(ID3LIBPATH);

			$this->getID3 = new getID3;

			/**
			 * LOOP THROUGH EACH FOLDER IN THE AUDIO DIRECTORY
			 */
			foreach(scandir($this->audio_path) as $dir)
			{
				$full_album_path = $this->audio_path.'/'.$dir;

				if (is_dir($full_album_path) && $dir != '.' && $dir != '..')
				{
					$album_cover_path = $full_album_path.'/coversm.jpg';

					if (file_exists($album_cover_path))
					{
						$album_cover_url = htmlentities($this->audio_url.'/'.$dir.'/coversm.jpg');
					}
					else {
						$album_cover_path = false;
						$album_cover_url = false;
					}

					$this->album_cover_url = $album_cover_url;

					$album_info = explode(' - ', $dir, 2);

					$album_index = $album_info[0];

					$album_name = $album_info[1];

					$album_name = explode(' - ', $album_name, 2);

					$album_name = $album_name[1];

					$album_songs = $this->get_album_songs($full_album_path, $album_name, $album_index);

					$this->album_paths["$album_index"] = array(
						'name'							=> $album_name,
						'path'							=> $full_album_path,
						'cover_path'				=> $album_cover_path,
						'cover_url'					=> $album_cover_url,
						'songs'							=> $album_songs,
						'album_has_lyrics'	=> $this->curr_album_has_lyrics,
						'zip_url'						=> $this->build_album_zip($album_name, $full_album_path)
					);
				}
			}

			/**
			 * SORT ALBUMS BY THE ARRAY KEY WHICH ALLOWS FOR LIST ORDERING
			 */

			ksort($this->album_paths, SORT_NUMERIC);

			$data = serialize(array(
				'album_paths'			=> $this->album_paths,
				'song_index'			=> $this->song_index,
				'song_time_index'	=> $this->song_time_index,
				'song_md5_index'	=> $this->song_md5_index
			));
			file_put_contents($this->cached_path, $data, LOCK_EX);
		} // end proces_albums()

		public function normalize_filename($file)
		{
			return sanitize_title_with_dashes($file);
		}

		private function build_album_zip($name, $album_dir)
		{
			$album_zip_filename = $this->normalize_filename($name).'.zip';
			$album_zip_path = $this->audio_path.'/'.$album_zip_filename;
			$album_zip_url = $this->audio_url.'/'.$album_zip_filename;
			$album_folder_full = basename($album_dir);
			$album_folder = substr($album_folder, 6, strlen($album_folder_full));

			if (isset($_GET['do_debug']))
			{
				var_dump($album_zip_filename, $album_zip_path);
			}

			// Exit if not album
			if (!file_exists($album_dir.'/coversm.jpg'))
				return false;

			// Don't process unles we have to
			if (file_exists($album_zip_path) && !isset($_GET['do_zip']))
					return $album_zip_path;

			$album_zip = new ZipArchive();

			if (!$album_zip->open($album_zip_path, ZIPARCHIVE::OVERWRITE))
			{
				echo "Failed to create archive:<br/> $album_zip_path\n";
				return false;
			}

			$album_zip->addEmptyDir($album_folder);

			$handle = opendir($album_dir);

	    while ($f = readdir($handle)) {
	      if (substr($f, 0, 1) !== '.') {
	        $filePath = "$album_dir/$f";
	        // Remove prefix from file path before add to zip.
	        $localPath = substr($filePath, 6+strlen($this->audio_path.'/'));
	        if (is_file($filePath)) {
	          $album_zip->addFile($filePath, $localPath);
	        }
	      }
	    }

		  closedir($handle);

			if (!$album_zip->status == ZIPARCHIVE::ER_OK)
			{
				echo "Failed to write files to zip:<br/> $album_zip_path\n";
				return false;
			}

			$album_zip->close();

			return $album_zip_url;


			$zipfilename  = "myarchive.zip"; // Default: "myarchive.zip"
			$timeout      = 5000           ; // Default: 5000

			// instantate an iterator (before creating the zip archive, just
			// in case the zip file is created inside the source folder)
			// and traverse the directory to get the file list.
			$dirlist = new RecursiveDirectoryIterator($sourcefolder);
			$filelist = new RecursiveIteratorIterator($dirlist);

			// set script timeout value
			ini_set('max_execution_time', $timeout);

			// instantate object
			$zip = new ZipArchive();

			// create and open the archive
			if ($zip->open("$zipfilename", ZipArchive::CREATE) !== TRUE) {
			    die ("Could not open archive");
			}

			// add each file in the file list to the archive
			foreach ($filelist as $key=>$value) {
			    $zip->addFile(realpath($key), $key) or die ("ERROR: Could not add file: $key");
			}

			// close the archive
			$zip->close();
			echo "Archive ". $zipfilename . " created successfully.";
		}

		/**
		 * LOOP THROUGH EACH TRACK AND COLLECT TAG INFORMATION
		 */
		public function get_album_songs($path, $album_name, $album_index)
		{
			$album_mp3s = array();

			$album_track_count = 0;

			if ( ! is_dir($path))
				return FALSE;

			if ( ! is_readable($path))
				return FALSE;

			$path = rtrim($path, '/').'/';

			$mp3_file_pattern = $path."*.mp3";

			$mp3_files = glob($mp3_file_pattern);

			$this->curr_lyrics_path = $path.$this->lyrics_path_mask;

			$this->curr_album_has_lyrics = ( is_dir( $this->curr_lyrics_path ) && is_readable( $this->curr_lyrics_path ) );

			foreach($mp3_files as $mp3_file)
			{
				$this->curr_mp3_data = $this->getID3->analyze($mp3_file);
				getid3_lib::CopyTagsToComments($this->curr_mp3_data);

				$album_track_count++;

				$lyrics_file = $this->curr_lyrics_path.'/'.$album_track_count.'.txt';

				$track_has_lyrics = ( $this->curr_album_has_lyrics )
													? file_exists ( $lyrics_file )
													: false;

				$str_audio_path = str_replace(array('\\', '/'), '/', realpath($this->audio_path));
				$track_url = htmlentities(str_replace($str_audio_path, $this->audio_url, $this->curr_mp3_data['filenamepath']));
				$encoded_url = urlencode($track_url);
				$tracked_out_file = $str__audio_path.str_replace('.mp3', '.zip', $this->curr_mp3_data['filenamepath']);
				$tracked_out = file_exists($tracked_out_file)
										 ? htmlentities(str_replace($str_audio_path, $this->audio_url, str_replace('.mp3', '.zip', $this->curr_mp3_data['filenamepath'])))
										 : null;

				$mp3_info = array(
					'path'							=> $this->get_track_data('filenamepath'),
					'created'						=> filectime( $mp3_file ),
					'modified'					=> filemtime( $mp3_file ),
					'track_url'					=> $track_url,
					'encoded_url'				=> $encoded_url,
					'md5'								=> hash_file('md5', $mp3_file),
					'track_num_total'		=> $this->get_track_data('track_number'),
					'artist'						=> $this->get_track_data('artist'),
					'title'							=> $this->get_track_data('title'),
					'album'							=> $this->get_track_data('album'),
					'genre'							=> $this->get_track_data('genre'),
					'length'						=> $this->get_track_data('playtime_string'),
					'bpm'								=> $this->get_track_data('bpm'),
					'year'							=> $this->get_track_data('year'),
					'has_lyrics'				=> $track_has_lyrics,
					'tracked_out'				=> $tracked_out
				);

				/**
				 * FILL IN PATH IF NOT FOUND IN TAGS
				 */
				if ( ! isset($mp3_info['path']) || (isset($mp3_info['path']) && empty($mp3_info['path'])))
				{
					$mp3_info['path'] = $mp3_file;
				}

				/**
				 * FILL IN TITLE FROM FILE NAME IF NOT FOUND IN TAGS
				 */
				if ( ! isset($mp3_info['title']) || (isset($mp3_info['title']) && empty($mp3_info['title'])))
				{
					$mp3_file2 = str_replace($path, '', $mp3_file);
					$pattern = '/^\d+\s?/i';
					$mp3_title = preg_replace($pattern, '', $mp3_file2);
					$mp3_title = ucwords($mp3_title);
					$mp3_info['title'] = $mp3_title;
				}

				/**
				 * FILL IN DEFAULT ARTIST IF NOT FOUND IN TAGS
				 */
				if ( ! isset($mp3_info['artist']) || (isset($mp3_info['artist']) && empty($mp3_info['artist'])))
				{
					$mp3_info['artist'] = $fallback_artist;
				}

				/**
				 * FILL IN DEFAULT GENRE IF NOT FOUND IN TAGS
				 */
				if ( ! isset($mp3_info['genre']) || (isset($mp3_info['genre']) && empty($mp3_info['genre'])))
				{
					$mp3_info['genre'] = $fallback_genre;
				}

				/**
				 * GET TRACK INDEX IF NOT FOUND IN TAGS
				 */
				if ( ! isset($mp3_info['track_number']) || (isset($mp3_info['track_number']) && empty($mp3_info['track_number'])))
				{
					$track_index = 1000 + $album_track_count;
				}
				else {
					preg_match('/\d+/i', $mp3_info['track_num_total'], $track_index_array);

					if (is_array($track_index_array) && ! empty($track_index_array))
					{
						$track_index_str = $track_index_array[0];
						$track_index = "000$track_index_str";
						$mp3_info['track_number'] = $track_index_str;

					}
				}

				// $mp3_info['cover_url'] = preg_replace('/\s{1}/', 'S_S_S', $this->album_cover_url);
				$mp3_info['cover_url'] = $this->album_cover_url;

				$this->song_index[ "$album_index_000$track_index_str" ]
				 = $this->song_time_index[ $mp3_info['modified']."$album_index_000$track_index_str" ]
				 = $this->song_md5_index[ $mp3_info['md5'] ]
				 = $mp3_info;

				$album_mp3s[$track_index] = $mp3_info;
			}

			/**
			 * ALBUM NEEDS TO BE SORTED BY KEYS
			 */
			ksort( $album_mp3s );
			ksort( $this->song_time_index );

			//ksort($this->song_time_index);

			return $album_mp3s;
		} // end get_album_songs()

		/**
		 * EXTRACT MP3 TAG FROM THE GETID3 RETURNED ARRAY
		 */
		public function get_track_data($data)
		{
			if ( ! is_array($this->curr_mp3_data))
				return '';

			if (empty($data) || empty($this->curr_mp3_data))
				return '';

			$good_data = $clean_data = FALSE;

			if (isset($this->curr_mp3_data['comments_html']) && isset($this->curr_mp3_data['comments_html']["$data"]))
			{
				$good_data = $this->curr_mp3_data['comments_html']["$data"];
			}
			elseif (isset($this->curr_mp3_data["$data"]))
			{
				$good_data = $this->curr_mp3_data["$data"];
			}

			if ($good_data !== FALSE)
			{
				if (is_array($good_data))
				{
					$clean_data = implode(', ', $good_data);
				}
				else {
					$clean_data = $good_data;
				}
			}
			else {
				$clean_data = '';
			}

			return $clean_data;
		} // end get_track_data()

		/**
		 * GET LYRICS
		 */
		public function get_lyrics($album_id, $track_id)
		{
			$album_cnt = 0;
			$album_directories = array();
			foreach(scandir($this->audio_path) as $dir)
			{
				if (substr($dir, 0, 1) !== '.')
				{
					$album_cnt++;
					$album_directories["$album_cnt"] = $this->audio_path.'/'.rtrim($dir, '\\/').'/lyrics';
				}
			}

			$lyric_file = $album_directories["$album_id"].'/'.$track_id.'.txt';

			if ( file_exists($lyric_file) )
			{
				$data = file_get_contents($lyric_file);
				$status = 1;
			}
			else {
				$data = 'empty';
				$status = 0;
			}

			$return = array(
				'status' => $status,
				'data'	 => $data
			);

			$this->lyrics = nl2br($data);

			//echo json_encode($return);
		}

		/**
		 * SHUTDOWN
		 */
		public function shutdown()
		{
		}
	}
?>