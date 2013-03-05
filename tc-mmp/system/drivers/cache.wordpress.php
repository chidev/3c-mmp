<?php defined('WPINC') or die('No direct script access.');

class TC_Cache_Wordpress_Driver implements TC_Cache_Driver
{
	public $cnf;
	
	public $cache_directory;
	
	public $cache_lifetime = 3600;
	
	private $preg_pattern;
	private $preg_pattern_mask;
	
	private $delete = array();
	private $found = array();
	private $loaded = array();
	
	public $global_tags = array();
	
	public function __construct(array $cnf = array())
	{
		$default = array(
			'cache_directory'			=> rtrim(TC_CACHE_PATH, '/'),
			'cache_lifetime'			=> 9999999
			);
			
		$this->cnf = array_merge($default, $cnf);
		
		$this->cache_directory = $this->cnf['cache_directory'];
		
		$this->cache_lifetime = $this->cnf['cache_lifetime'];
		
		if ( ! is_dir($this->cache_directory))
		{
			die('Fatal Error: Can\' load cache directory(' . $this->cache_directory . ')');
		}
		else {
			$ht_location = $this->cache_directory . '/' . '.htaccess';
			
			if ( ! file_exists($ht_location))
			{
				$htaccess = fopen($ht_location, 'w');
				fwrite($htaccess, "order allow,deny\r\ndeny from all");
				fclose($htaccess);
			}
		}
		
		$this->preg_pattern_mask = '/cache\.{CACHE_ID}\.{CACHE_TAGS}\.([0-9]+)/i';
	}
	
	public function get_preg($cache_id = null, array $cache_tags = array())
	{
		if ( ! $cache_id && empty($cache_tags)) return null;
		
		if ($cache_id == 'all')
		{
			return '/cache\.([^.]+)\.([^.]+)\.([\d]+)/i';
		}
		
		$preg_id = $preg_tag = '([^.]+)';
		
		switch (empty($cache_tags))
		{
			case true:
				$preg_id  = $cache_id;
				break;
			case false:
				$preg_tag = '(' . implode('|', $cache_tags) . ')';
				break;
		}
		
		$this->preg_pattern = str_replace(
			array('{CACHE_ID}', '{CACHE_TAGS}'),
			array($preg_id, $preg_tag),
			$this->preg_pattern_mask
		);
		
		return $this->preg_pattern;
	}
	public function save($cache_id, $cache_data, array $cache_tags = array(), $expires)
	{
		//var_dump($this->delete($cache_id));
		
		$all_tags = array_merge($this->global_tags, $cache_tags);
		
		$cache_tags_string = implode('-', $all_tags);
		
		$cache_file = implode('.', array('cache', $cache_id, $cache_tags_string, time()+$expires));
		
		$cache_location = $this->cache_directory.'/'.$cache_file;
		
		$cache_handle = fopen($cache_location, 'w'); 
		
		fwrite($cache_handle, serialize($cache_data));
		
		fclose($cache_handle);
		
		/* DEBUG:
		echo '<div style="padding:20px; background:#efefef;font-size:16px;color:#333;text-align:left;"><pre>';
		var_dump($cache_file);
		echo "\n\n";
		echo '</pre></div>';*/
		
		return true;
	}
		
	public function cache_list($cache_id, array $cache_tags = array())
	{
		$this->get_preg($cache_id, $cache_tags);
		
		$cache_directory = dir($this->cache_directory);
		
		while ($file = $cache_directory->read())
		{
			if (preg_match($this->preg_pattern, $file, $cache_parts))
			{
				if (isset($cache_id) && $cache_id)
				{
					$cache_tags = explode('-', $cache_parts[1]);
				}
				elseif (isset($cache_tags) && $cache_tags)
				{
					$cache_id = $cache_parts[1];
				}
				
				$x = 1;
				if ($cache_id == 'all')
				{
					$cache_id = $cache_parts[1];
					$cache_tags = explode('-', $cache_parts[2]);
					$x = 2;
				}
				
				$cache_expires = $cache_parts[$x+1];
				$cache_created = $cache_parts[$x+2];
				
				// Full location 
				$cache_location = $this->cache_directory . '/' . $file; 
					
				// check if cache has expired:
				if ($cache_created > (time() + $cache_lifetime))
				{
					$this->delete[] = $cache_parts[1];
				}
				else {
					$this->found[$cache_id] = $cache_location;
				}
			}
		}
		
		$cache_directory->close();
	}
	
	public function get($cache_id, array $cache_tags = array())
	{
		$this->cache_list($cache_id, $cache_tags);
		
		foreach ($this->found as $cache_id => $cache_location)
		{
			$cache_file = fopen($cache_location, 'r');  
			$cache_size = filesize($cache_location);
			
			$cache_data = unserialize(fread($cache_file, $cache_size));
			
			fclose($cache_file);
			
			$this->loaded[$cache_id] = array(
				'cache'			=> $cache_data,
				'zipped'		=> false,
				'size'			=> $cache_size);
			
			if (isset($this->loaded[$cache_id]['zipped']['driver']))
			{
				$unzipped = call_user_func($this->loaded[$cache_id]['zipped'], $this->loaded[$cache_id]['cache']);
				$this->loaded[$cache_id]['cache'] = $unzipped;
			}
		}
		
		if (isset($this->loaded))
		{
			return $this->loaded;
		}
		
		return false;
	}
	
	public function clean()
	{
		$this->cache_list('all');
		
		if (count($this->delete) === 0) return 0;
		
		$this->delete($delete);
		
		return false;
	}
		
	public function delete($cache_id, array $cache_tags = array())
	{
		if (is_array($cache_id) && $count = count($cache_id))
		{
			foreach($cache_id as $id)
			{
				$this->delete($id);
			}
			return $count;
		}
		
		$cache_directory = dir($this->cache_directory);
		
		$preg_pattern = $this->get_preg($cache_id, $cache_tags);
		
		while ($file = $cache_directory->read())
		{
			$file_location = $this->cache_directory . '/' . $file;
			
			if (preg_match($preg_pattern, $file) && file_exists($file_location))
			{
				unlink($file_location);
				
				return true;
			}
		}
		
		$cache_directory->close();
		
		return false;
	}
} // End pa_cache_filesystem
?>