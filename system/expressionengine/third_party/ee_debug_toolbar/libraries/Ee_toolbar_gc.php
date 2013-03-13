<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * mithra62 - EE Debug Toolbar
 *
 * @package        mithra62:EE_debug_toolbar
 * @author         Eric Lamb
 * @copyright      Copyright (c) 2013, mithra62, Eric Lamb.
 * @link           http://mithra62.com/
 * @updated        1.0
 * @filesource     ./system/expressionengine/third_party/ee_debug_toolbar/
 */

/**
 * Garbage Collection Library
 *
 * Library
 *
 * @package        mithra62:EE_debug_toolbar
 * @author         Eric Lamb
 * @filesource     ./system/expressionengine/third_party/ee_debug_toolbar/libraries/Ee_toolbar_gc.php
 */
class Ee_toolbar_gc
{
	/**
	 * How long the cache is allowed to live
	 * @var int
	 */
	public $expires = 86400;
	
	/**
	 * Where the files live we want to monitor
	 * @var string
	 */
	public $cache_dir = '';
	
	public function __construct()
	{
		$this->EE =& get_instance();
		$this->cache_dir = APPPATH.'cache/eedt/';
	}
	
	/**
	 * Checks if a given $file has a modified date outside the $expires range
	 * @param string $file
	 */
	private function expired($file)
	{
		$cache_created = filemtime($file);
		$max_time = time()-$this->expires;
		if($max_time >= $cache_created)
		{
			return TRUE;
		}
	}
	
	/**
	 * Wrapper to remove a cached file
	 * @param string $file
	 * @return boolean
	 */
	private function delete($file)
	{
		return unlink($file);
	}
	
	/**
	 * Do It!!
	 */
	public function run()
	{
		if(is_really_writable($this->cache_dir))
		{
			$d = dir($this->cache_dir);
			while (false !== ($entry = $d->read()))
			{
				if($entry == '.' || $entry == '..')
				{
					continue;
				}
					
				$file = $this->cache_dir.$entry;
				if(!is_really_writable($file))
				{
					continue; //can't write; don't care
				}
	
				if($this->expired($file))
				{
					$this->delete($file);
				}
			}
			$d->close();
		}
	}	
}