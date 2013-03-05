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
 * Slighltly modified version of Kohana 3's core remote lib.
 */

class KRemote
{
	// Default curl options
	public static $default_options = array
	(
		CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; TCCorp v2.0 +http://graphics.ms/)',
		CURLOPT_CONNECTTIMEOUT => 5,
		CURLOPT_TIMEOUT        => 5,
	);
	
	/**
	 * Returns the output of a remote URL.
	 *
	 * @throws  Exception
	 * @param   string   remote URL
	 * @param   array    curl options
	 * @return  array
	 */
	public static function get($url, array $options = NULL)
	{
		if ($options === NULL)
		{
			// Use default options
			$options = KRemote::$default_options;
		}
		else
		{
			// Add default options
			$options = $options + KRemote::$default_options;
		}
		
		// The transfer must always be returned
		$options[CURLOPT_RETURNTRANSFER] = TRUE;
		
		// Open a new remote connection
		$remote = curl_init($url);
		
		// Set connection options
		curl_setopt_array($remote, $options);
		
		/*echo '<div style="padding:20px; background:#efefef;font-size:16px;color:#333;text-align:left;"><pre>';
		var_dump($options);
		echo '</pre></div>';
		die;*/
		
		// Get the response
		$response = curl_exec($remote);
		
		// Get the response information
		$code = curl_getinfo($remote, CURLINFO_HTTP_CODE);
		
		if ($code < 200 OR $code > 299)
		{
			$error = $response;
		}
		elseif ($response === FALSE)
		{
			$error = curl_error($remote);
		}
		
		// Close the connection
		curl_close($remote);
		
		if (isset($error))
		{
			return array('code' => $code, 'error' => $error);
		}
		
		return array('code' => $code, 'body' => $response);
	}
	
	/**
	 * Returns the status code for a URL.
	 *
	 * @param   string  URL to check
	 * @return  integer
	 */
	public static function status($url, $headers)
	{
		// Get the hostname and path
		$url = parse_url($url);
		
		if (empty($url['path']))
		{
			// Request the root document
			$url['path'] = '/';
		}
		
		// Open a remote connection
		$port = isset($url['port']) ? $url['port'] : 80;
		$remote = fsockopen($url['host'], $port, $errno, $errstr, 5);
		
		if ( ! is_resource($remote))
			return FALSE;
		
		// Set CRLF
		$CRLF = "\r\n";
		
		// Send request
		fwrite($remote, 'HEAD '.$url['path'].' HTTP/1.0'.$CRLF);
		fwrite($remote, 'Host: '.$url['host'].$CRLF);
		fwrite($remote, 'Connection: close'.$CRLF);
		
		// Send custom headers
		$had_ua = FALSE;
		foreach($headers as $hdr)
		{
			$had_ua === FALSE and $had_ua = (strpos($hdr, 'User-Agent') !== FALSE);
			fwrite($remote, $hdr.$CRLF); endforeach;
		}
		
		// Send user agent if not already sent
		if ($had_ua)
		{
			fwrite($remote, 'User-Agent: Mozilla/5.0 (compatible; PurlAgent v1.0 +http://purlagent.com/)'.$CRLF);
		}
		
		// Send one more CRLF to terminate the headers
		fwrite($remote, $CRLF);
		
		// Remote is offline
		$response = FALSE;
		
		while ( ! feof($remote))
		{
			// Get the line
			$line = trim(fgets($remote, 512));
			
			if ($line !== '' AND preg_match('#^HTTP/1\.[01] (\d{3})#', $line, $matches))
			{
				// Response code found
				$response = (int) $matches[1];
				break;
			}
		}
		
		// Close the connection
		fclose($remote);
		
		return $response;
	}
		
	final private function __construct()
	{
		// This is a static class
	}
	
} // End remote
?>