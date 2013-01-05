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
 * File-based Cache driver.
 *
 * $Id: File.php 4046 2009-03-05 19:23:29Z Shadowhand $
 *
 * @package    Cache
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class TC_Cache_File_Driver implements TC_Cache_Driver {

	protected $directory = '';

	/**
	 * Tests that the storage location is a directory and is writable.
	 */
	public function __construct($directory)
	{
		// Find the real path to the directory
		$directory = str_replace('\\', '/', realpath($directory)).'/';

		// Make sure the cache directory is writable
		if ( ! is_dir($directory) OR ! is_writable($directory))
			throw Exception('unwritable', $directory);

		// Directory is valid
		$this->directory = $directory;
	}

	/**
	 * Finds an array of files matching the given id or tag.
	 *
	 * @param  string  cache id or tag
	 * @param  bool    search for tags
	 * @return array   of filenames matching the id or tag
	 */
	public function exists($id, $tag = FALSE)
	{
		if ($id === TRUE)
		{
			// Find all the files
			return glob($this->directory.'*~*~*');
		}
		elseif ($tag === TRUE)
		{
			// Find all the files that have the tag name
			$paths = glob($this->directory.'*~*'.$id.'*~*');

			// Find all tags matching the given tag
			$files = array();
			foreach ($paths as $path)
			{
				// Split the files
				$tags = explode('~', basename($path));

				// Find valid tags
				if (count($tags) !== 3 OR empty($tags[1]))
					continue;

				// Split the tags by plus signs, used to separate tags
				$tags = explode('+', $tags[1]);

				if (in_array($tag, $tags))
				{
					// Add the file to the array, it has the requested tag
					$files[] = $path;
				}
			}

			return $files;
		}
		else
		{
			// Find the file matching the given id
			return glob($this->directory.$id.'~*');
		}
	}

	/**
	 * Sets a cache item to the given data, tags, and lifetime.
	 *
	 * @param   string   cache id to set
	 * @param   string   data in the cache
	 * @param   array    cache tags
	 * @param   integer  lifetime
	 * @return  bool
	 */
	public function set($id, $data, array $tags = NULL, $lifetime)
	{
		// Remove old cache files
		$this->delete($id);

		// Cache File driver expects unix timestamp
		if ($lifetime !== 0)
		{
			$lifetime += time();
		}

		if ( ! empty($tags))
		{
			// Convert the tags into a string list
			$tags = implode('+', $tags);
		}

		// Write out a serialized cache
		return (bool) file_put_contents($this->directory.$id.'~'.$tags.'~'.$lifetime, serialize($data));
	}

	/**
	 * Finds an array of ids for a given tag.
	 *
	 * @param  string  tag name
	 * @return array   of ids that match the tag
	 */
	public function find($tag)
	{
		// An array will always be returned
		$result = array();

		if ($paths = $this->exists($tag, TRUE))
		{
			// Length of directory name
			$offset = strlen($this->directory);

			// Find all the files with the given tag
			foreach ($paths as $path)
			{
				// Get the id from the filename
				list($id, $junk) = explode('~', basename($path), 2);

				if (($data = $this->get($id)) !== FALSE)
				{
					// Add the result to the array
					$result[$id] = $data;
				}
			}
		}

		return $result;
	}

	/**
	 * Fetches a cache item. This will delete the item if it is expired or if
	 * the hash does not match the stored hash.
	 *
	 * @param   string  cache id
	 * @return  mixed|NULL
	 */
	public function get($id)
	{
		if ($file = $this->exists($id))
		{
			// Use the first file
			$file = current($file);

			// Validate that the cache has not expired
			if ($this->expired($file))
			{
				// Remove this cache, it has expired
				$this->delete($id);
			}
			else
			{
				// Turn off errors while reading the file
				$ER = error_reporting(0);

				if (($data = file_get_contents($file)) !== FALSE)
				{
					// Unserialize the data
					$data = unserialize($data);
				}
				else
				{
					// Delete the data
					unset($data);
				}

				// Turn errors back on
				error_reporting($ER);
			}
		}

		// Return NULL if there is no data
		return isset($data) ? $data : NULL;
	}

	/**
	 * Deletes a cache item by id or tag
	 *
	 * @param   string   cache id or tag, or TRUE for "all items"
	 * @param   boolean  use tags
	 * @return  boolean
	 */
	public function delete($id, $tag = FALSE)
	{
		$files = $this->exists($id, $tag);

		if (empty($files))
			return FALSE;

		// Disable all error reporting while deleting
		$ER = error_reporting(0);

		foreach ($files as $file)
		{
			// Remove the cache file
			@unlink($file);
		}

		// Turn on error reporting again
		error_reporting($ER);

		return TRUE;
	}

	/**
	 * Deletes all cache files that are older than the current time.
	 *
	 * @return void
	 */
	public function delete_expired()
	{
		if ($files = $this->exists(TRUE))
		{
			// Disable all error reporting while deleting
			$ER = error_reporting(0);

			foreach ($files as $file)
			{
				if ($this->expired($file))
				{
					// The cache file has already expired, delete it
					if ( ! unlink($file))
						Kohana::log('error', 'Cache: Unable to delete cache file: '.$file);
				}
			}

			// Turn on error reporting again
			error_reporting($ER);
		}
	}

	/**
	 * Check if a cache file has expired by filename.
	 *
	 * @param  string  filename
	 * @return bool
	 */
	protected function expired($file)
	{
		// Get the expiration time
		$expires = (int) substr($file, strrpos($file, '~') + 1);

		// Expirations of 0 are "never expire"
		return ($expires !== 0 AND $expires <= time());
	}

} // End Cache File Driver