<?php defined('TC_SYSTEM_PATH') OR die('No direct access allowed.<br/>'.__FILE__);
/*
Kohana License Agreement

This license is a legal agreement between you and the Kohana Software Foundation for the use of Kohana Framework (the "Software"). By obtaining the Software you agree to comply with the terms and conditions of this license.

Copyright © 2007–2010 Kohana Team
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

    * Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
    * Neither the name of the Kohana nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

NOTE: This license is modeled after the BSD software license.
*/

/**
 * 3rd Corner Studios Wordpress Framework
 *
 * @package			TC_WP_001
 */
 
/**
 * Provides a driver-based interface for finding, creating, and deleting cached
 * resources. Caches are identified by a unique string. Tagging of caches is
 * also supported, and caches can be found and deleted by id or tag.
 *
 * $Id: Cache.php 4321 2009-05-04 01:39:44Z kiall $
 *
 * @package    Cache
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class TC_Cache {

	protected static $instances = array();

	// For garbage collection
	protected static $loaded;

	// Configuration
	protected $config;

	// Driver object
	protected $driver;

	/**
	 * Returns a singleton instance of Cache.
	 *
	 * @param   string  configuration
	 * @return  Cache_Core
	 */
	public static function & instance($config = FALSE)
	{
		if ( ! isset(TC_Cache::$instances[$config]))
		{
			// Create a new instance
			TC_Cache::$instances[$config] = new Cache($config);
		}

		return TC_Cache::$instances[$config];
	}
	
	/**
	 * Loads the configured driver and validates it.
	 *
	 * @param   array|string  custom configuration or config group name
	 * @return  void
	 */
	public function __construct($config = FALSE)
	{
		if (is_string($config))
		{
			$name = $config;
			
			// Test the config group name
			($cnf = TC::config('tc.'.$config)) and $config = $cnf;
		}
		
		if (is_array($config))
		{
			// Append the default configuration options
			$config += TC::config('cache');
		}
		else
		{
			// Load the default group
			$config = TC::config('cache');
		}
		
		// Cache the config in the object
		$this->config = $config;
		
		// Load the driver
		require_once(dirname(__FILE__).'/drivers/cache.driver.php');
		
		// Set driver name
		$driver = 'TC_Cache_'.ucfirst($this->config['driver']).'_Driver';
		
		if ($file = dirname(__FILE__).'/drivers/cache.'.$this->config['driver'].'.php' && file_exists($file))
		{
			// Load the driver
			require_once($file);
		}
		else {
			$driver = 'TC_Cache_File_driver';
			require_once(dirname(__FILE__).'/drivers/cache.filesystem.php');
		}
		
		// Initialize the driver
		$this->driver = new $driver($this->config['save_path']);
		
		// Validate the driver
		if ( ! ($this->driver instanceof TC_Cache_Driver))
		{
			die("[TCWPERR] Couldn't load driver: $driver");
		}
		
		if (TC_Cache::$loaded !== TRUE)
		{
			$this->config['requests'] = (int) $this->config['requests'];
			
			if ($this->config['requests'] > 0 AND mt_rand(1, $this->config['requests']) === 1)
			{
				// Do garbage collection
				$this->driver->delete_expired();
			}
			
			// Cache has been loaded once
			TC_Cache::$loaded = TRUE;
		}
	}

	/**
	 * Fetches a cache by id. NULL is returned when a cache item is not found.
	 *
	 * @param   string  cache id
	 * @return  mixed   cached data or NULL
	 */
	public function get($id)
	{
		// Sanitize the ID
		$id = $this->sanitize_id($id);

		return $this->driver->get($id);
	}

	/**
	 * Fetches all of the caches for a given tag. An empty array will be
	 * returned when no matching caches are found.
	 *
	 * @param   string  cache tag
	 * @return  array   all cache items matching the tag
	 */
	public function find($tag)
	{
		return $this->driver->find($tag);
	}

	/**
	 * Set a cache item by id. Tags may also be added and a custom lifetime
	 * can be set. Non-string data is automatically serialized.
	 *
	 * @param   string        unique cache id
	 * @param   mixed         data to cache
	 * @param   array|string  tags for this item
	 * @param   integer       number of seconds until the cache expires
	 * @return  boolean
	 */
	function set($id, $data, $tags = NULL, $lifetime = NULL)
	{
		if (is_resource($data))
			throw new Exception('Uh oh, no resources in cache.');

		// Sanitize the ID
		$id = $this->sanitize_id($id);

		if ($lifetime === NULL)
		{
			// Get the default lifetime
			$lifetime = $this->config['lifetime'];
		}

		return $this->driver->set($id, $data, (array) $tags, $lifetime);
	}

	/**
	 * Delete a cache item by id.
	 *
	 * @param   string   cache id
	 * @return  boolean
	 */
	public function delete($id)
	{
		// Sanitize the ID
		$id = $this->sanitize_id($id);

		return $this->driver->delete($id);
	}

	/**
	 * Delete all cache items with a given tag.
	 *
	 * @param   string   cache tag name
	 * @return  boolean
	 */
	public function delete_tag($tag)
	{
		return $this->driver->delete($tag, TRUE);
	}

	/**
	 * Delete ALL cache items items.
	 *
	 * @return  boolean
	 */
	public function delete_all()
	{
		return $this->driver->delete(TRUE);
	}

	/**
	 * Replaces troublesome characters with underscores.
	 *
	 * @param   string   cache id
	 * @return  string
	 */
	protected function sanitize_id($id)
	{
		// Change slashes and spaces to underscores
		return str_replace(array('/', '\\', ' '), '_', $id);
	}

} // End Cache
