<?php

if(!function_exists('eedt_output_array'))
{
	/**
	 * Formats $arr for use in Panels with Array data
	 * @param array $arr
	 * @param string $default
	 * @param string $pair_delim
	 * @param string $tail_delim
	 * @return string
	 */
	function eedt_output_array($arr, $default = 'nothing_found', $pair_delim = ' =&gt; ', $tail_delim = '<br />')
	{
		if(!is_array($arr) || count($arr) == '0')
		{
			return lang($default);
		}
		
		$return = '';
		foreach($arr AS $key => $value)
		{
			if(is_array($value))
			{
				$return .= $key.$pair_delim.'<pre>'.print_r($value, TRUE).'</pre>';
			}
			else
			{
				$return .= $key.$pair_delim.$value.$tail_delim;
			}
		}
		
		return $return;
	}
}

if(!function_exists('eedt_theme_url'))
{
	/**
	 * Sets up the third party theme URL
	 * @return string
	 */
	function eedt_theme_url()
	{
		$url = '';
		if(defined('URL_THIRD_THEMES'))
		{
			$url = URL_THIRD_THEMES;
		}
		else 
		{
			$ee =& get_instance();
			$url = rtrim($ee->config->config['theme_folder_url'], '/') .'/third_party/';
		}
		
		return $url;
	}
}

if(!function_exists('eedt_theme_path'))
{
	/**
	 * Sets up the third party themes path
	 * @return string
	 */
	function eedt_theme_path()
	{
		$path = '';
		if(defined('PATH_THIRD_THEMES'))
		{
			$path = PATH_THIRD_THEMES;
		}
		else
		{
			$ee =& get_instance();
			$path = rtrim($ee->config->config['theme_folder_path'], '/') .'/third_party/';
		}

		return $path;
	}
}

if(!function_exists('eedt_third_party_path'))
{
	/**
	 * Sets up the third party add-ons path
	 * @return string
	 */
	function eedt_third_party_path()
	{
		$path = '';
		if(defined('PATH_THIRD'))
		{
			$path = PATH_THIRD;
		}
		else
		{
			$path = APPPATH.'third_party/';
		}

		return $path;
	}
}