<?  

/** 

class.cacher.php -- Class used to cache a variable in serialized form. 

Written by Serge Stepanov (serge_AT_gfxcafe.com). 
Feel free to email me with questions. If you find this useful, would be great to hear from you. 

version 1.0 - 

Changes: 
- First release. 

Notes: 
- Get() method will only use 
	the first occurrence, after which 
	the loop will be broken. 

Example: 
			
include("class.cacher.php"); 

$cache = new Cacher; 
// Get var if not 60 seconds old 
$variable = $cache->Get("variable_with_id", 60); 

if (!$variable) { 
		// Cache file expired or is inexistant 
		// Do something to get new data 
		$cache->Set("variable_with_id", $newdata); 
		$variable = $newdata; 
} 

echo $variable; 

**/  

class Cacher
{
		// Where things are cached to (must have trailing slash!) 
		var $cacheDir = TC_CACHE_PATH;
		// How long to cache something for in seconds, default 1hr
		var $defaultCacheLife = "3600"; 
			
		/** 
				Set($varId, $varValue) -- 
				Creates a file named "cache.VARID.TIMESTAMP" 
				and fills it with the serialized value from $varValue. 
				If a cache file with the same varId exists, Delete() 
				will remove it. 
		**/  
		function Set($varId, $varValue) {  
				// Clean up old caches with same varId 
				$this->Delete($varId);
				// Create new file 
				$fileHandler = fopen(dirname(__FILE__).$this->cacheDir . "cache." . $varId . "." . time(), "a"); 
				// Write serialized data 
				fwrite($fileHandler, serialize($varValue));  
				fclose($fileHandler);  
		}  
			
		/** 
				Get($varID, $cacheLife) -- 
				Retrives the value inside a cache file 
				specified by $varID if the expiration time 
				(specified by $cacheLife) is not over. 
				If expired, returns FALSE 
		**/  
		function Get($varId, $cacheLife="") {  
				// Set default cache life 
				$cacheLife = (!empty($cacheLife)) ? $cacheLife : $this->defaultCacheLife; 
					
				/* Loop through the directory looking for cache file */  
				$dirHandler = dir(dirname(__FILE__).$this->cacheDir);  
				while ($file = $dirHandler->read()) {  
						/* Check for cache file with requested varId */  
						if (preg_match("/cache.$varId.[0-9]/", $file)) {  
								$cacheFileName = explode(".", $file);  
								// Cache file creation time 
								$cacheFileLife = $cacheFileName[2];  
								// Full location 
								$cacheFile = dirname(__FILE__).$this->cacheDir . $file; 
									
								/* Check to see if cache file has expired or not */  
								if ((time() - $cacheFileLife) < $cacheLife) {  
										$fileHandler = fopen($cacheFile, "r");  
										$varValueResult = fread($fileHandler, filesize($cacheFile));  
										fclose($fileHandler);  
										// Still good, return unseralized data 
										return unserialize($varValueResult);  
								} else {  
										// Cache expired, break loop 
										break;   
								}  
						}  
				}  
				$dirHandler->close();
				return FALSE;  
		}  
			
		/** 
				Delete($varId) -- 
				Loops through the cache directory and 
				removes any cache files with the varId 
				specified in $varID 
		**/      
		function Delete($varId) {  
				$dirHandler = dir(dirname(__FILE__).$this->cacheDir);  
				while ($file = $dirHandler->read()) {  
						if (preg_match("/cache.$varId.[0-9]/", $file)) {  
								unlink(dirname(__FILE__).$this->cacheDir . $file); // Delete cache file 
						}  
				}  
				$dirHandler->close();
		}
		
}

?>