<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * mithra62 - EE Debug Toolbar
 *
 * @package        mithra62:EE_debug_toolbar
 * @author         Eric Lamb
 * @copyright      Copyright (c) 2012, mithra62, Eric Lamb.
 * @link           http://mithra62.com/
 * @updated        1.0
 * @filesource     ./system/expressionengine/third_party/nagger/
 */

/**
 * Toolbar Library
 *
 * Extension class
 *
 * @package        mithra62:EE_debug_toolbar
 * @author         Eric Lamb
 * @filesource     ./system/expressionengine/third_party/ee_debug_toolbar/libraries/Toolbar.php
 */
class Toolbar
{

	var $default_theme = "default";

	public function __construct()
	{
		$this->EE = & get_instance();
	}
	
	public function get_settings()
	{
		if (!isset($this->EE->session->cache['ee_debug_toolbar']['settings']))
		{
			$this->EE->load->model('ee_debug_settings_model', 'debug_settings');
			$this->EE->session->cache['ee_debug_toolbar']['settings'] = $this->EE->debug_settings->get_settings();
		}
	
		return $this->EE->session->cache['ee_debug_toolbar']['settings'];
	}	
	
	/**
	 * Takes the included files and breaks up into mutli arrays for use in the debugger
	 * @param array $files
	 * @return Ambigous <multitype:unknown , unknown>
	 */
	public function setup_files(array $files)
	{
		sort($files);

		$path_third         = realpath(PATH_THIRD);
		$path_ee            = realpath(APPPATH);
		$path_first_modules = realpath(PATH_MOD);
		$bootstrap_file     = FCPATH . SELF;
		$return             = array();
		foreach ($files AS $file) {
			if (strpos($file, $path_third) === 0) {
				$return['third_party_addon'][] = $file;
				continue;
			}

			if (strpos($file, $path_first_modules) === 0) {
				$return['first_party_modules'][] = $file;
				continue;
			}

			if (strpos($file, $bootstrap_file) === 0) {
				$return['bootstrap_file'] = $file;
				continue;
			}

			if (strpos($file, $path_ee) === 0) {
				$return['expressionengine_core'][] = $file;
				continue;
			}

			$return['other_files'][] = $file;

		}

		return $return;
	}

	public function setup_queries()
	{
		$dbs = array();

		// Let's determine which databases are currently connected to
		foreach (get_object_vars($this->EE) as $EE_object) {
			if (is_object($EE_object) && is_subclass_of(get_class($EE_object), 'CI_DB')) {
				$dbs[] = $EE_object;
			}
		}

		$output = array();
		if (count($dbs) == 0) {
			return $output;
		}
		// Load the text helper so we can highlight the SQL
		$this->EE->load->helper('text');

		$count      = 0;
		$total_time = 0;
		foreach ($dbs as $db) {
			$count++;

			if (count($db->queries) != 0) {
				foreach ($db->queries as $key => $val) {
					$total_time          = $total_time + $db->query_times[$key];
					$time                = number_format($db->query_times[$key], 4);
					$output['queries'][] = array('query' => highlight_code($val, ENT_QUOTES), 'time' => $time);
				}
			}

		}

		$output['total_time'] = number_format($total_time, 4);

		return $output;
	}

	public function setup_benchmarks()
	{
		$profile = array();
		foreach ($this->EE->benchmark->marker as $key => $val) {
			// We match the "end" marker so that the list ends
			// up in the order that it was defined
			if (preg_match("/(.+?)_end/i", $key, $match)) {
				if (isset($this->EE->benchmark->marker[$match[1] . '_end']) AND isset($this->EE->benchmark->marker[$match[1] . '_start'])) {
					$profile[$match[1]] = $this->EE->benchmark->elapsed_time($match[1] . '_start', $key);
				}
			}
		}

		return $profile;
	}

	/**
	 * Breaks up the template log data to create the chart
	 *
	 * @param array $log
	 * @return multitype:
	 */
	public function format_tmpl_log(array $log)
	{
		$return = array();
		foreach ($log AS $item) {
			$parts = explode(') ', $item, 2);
			if (!isset($parts['0']) || !isset($parts['1'])) {
				continue;
			}

			array_map('trim', $parts);
			$perf    = str_replace('(', '', $parts['0']);
			$tooltip = strip_tags(htmlentities($parts['1']));

			//now we have to fix some munged up values
			$replace = array("&amp;nbsp;", "-&gt;", "&quot;");
			$with    = array("-", "->", "\"");
			$tooltip = str_replace($replace, $with, $tooltip);

			$parts    = explode(' / ', $perf);
			$return[] = array(
				'time'   => $parts['0'],
				'memory' => (float)$parts['1'],
				'desc'   => $tooltip
			);
		}

		return $return;
	}

	/**
	 * Returns a JSON string that can be used by the Template Chart JS
	 *
	 * @param $log array
	 * @return string
	 */
	public function format_tmpl_chart_json($data)
	{
		return json_encode($data);
	}


	/**
	 * Format a number of bytes into a human readable format.
	 * Optionally choose the output format and/or force a particular unit
	 *
	 * @param   int     $bytes      The number of bytes to format. Must be positive
	 * @param   string  $format     Optional. The output format for the string
	 * @param   string  $force      Optional. Force a certain unit. B|KB|MB|GB|TB
	 * @return  string              The formatted file size
	 */
	public function filesize_format($val, $digits = 3, $mode = "SI", $bB = "B")
	{ //$mode == "SI"|"IEC", $bB == "b"|"B"

		$si  = array("", "k", "M", "G", "T", "P", "E", "Z", "Y");
		$iec = array("", "Ki", "Mi", "Gi", "Ti", "Pi", "Ei", "Zi", "Yi");
		switch (strtoupper($mode)) {
			case "SI" :
				$factor  = 1000;
				$symbols = $si;
				break;
			case "IEC" :
				$factor  = 1024;
				$symbols = $iec;
				break;
			default :
				$factor  = 1000;
				$symbols = $si;
				break;
		}
		switch ($bB) {
			case "b" :
				$val *= 8;
				break;
			default :
				$bB = "B";
				break;
		}
		for ($i = 0; $i < count($symbols) - 1 && $val >= $factor; $i++) {
			$val /= $factor;
		}
		$p = strpos($val, ".");
		if ($p !== false && $p > $digits) {
			$val = round($val);
		} elseif ($p !== false) {
			$val = round($val, $digits - $p);
		}

		return round($val, $digits) . " " . $symbols[$i] . $bB;
	}	
	
	public function get_themes()
	{
		$path = (defined('PATH_THIRD_THEMES') ? PATH_THIRD_THEMES : rtrim($this->EE->config->item('theme_folder_path'), '/')).'/ee_debug_toolbar/themes/';
		$d = dir($path);
		$themes = array();
		$bad = array('.', '..');
		while (false !== ($entry = $d->read()))
		{
			if(is_dir($path.$entry) && !in_array($entry, $bad))
			{
				$name = ucwords(str_replace('_', ' ', $entry));
				$themes[$entry] = $name;
			}
		}
		$d->close();
		return $themes;		
	}
	
	/**
	 * Create Theme CSS URL
	 *
	 * @param string $theme
	 * @return string
	 */
	public function create_theme_url($theme, $sub_dir = '')
	{
		if (is_dir(PATH_THIRD_THEMES . "ee_debug_toolbar/themes/" . $theme . "/$sub_dir/")) {
			return URL_THIRD_THEMES . "ee_debug_toolbar/themes/" . $theme . "/$sub_dir/";
		}
		return URL_THIRD_THEMES . "ee_debug_toolbar/themes/" . $this->default_theme . "/$sub_dir/";
	}	
}