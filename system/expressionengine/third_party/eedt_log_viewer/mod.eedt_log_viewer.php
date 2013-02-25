<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * mithra62 - EE Debug Toolbar
 *
 * @package        mithra62:EE_debug_toolbar
 * @author         Eric Lamb
 * @copyright      Copyright (c) 2013, mithra62, Eric Lamb.
 * @link           http://mithra62.com/
 * @updated        1.0
 * @filesource     ./system/expressionengine/third_party/eedt_log_viewer/
 */

/**
 * EE Debug Toolbar - Memory History Module Class
 *
 * Module class
 *
 * @package        mithra62:EE_debug_toolbar
 * @author         Eric Lamb
 * @filesource     ./system/expressionengine/third_party/eedt_log_viewer/mod.eedt_log_viewer.php
 */
class Eedt_log_viewer 
{
	
	/**
	 * The data to return from the module
	 * @var stirng
	 */
	public $return_data	= '';
	

	
	public function __construct()
	{
		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();
		$path = dirname(realpath(__FILE__));
		include $path.'/config'.EXT;
		$this->class = $config['class_name'];
		$this->version = $config['version'];
		
		$this->EE->lang->loadfile('eedt_log_viewer');
		$this->EE->load->add_package_path(PATH_THIRD . 'ee_debug_toolbar/');
		$this->EE->load->add_package_path(PATH_THIRD . 'eedt_log_viewer/');		
	}
	
	public function action_test()
	{
		
	}
	
	public function get_panel_logs()
	{

		$log_path = $this->EE->config->config['log_path'];
		if(!is_readable($log_path))
		{
			echo lang('log_dir_not_readable');
			exit;
		}	

		//get the log files
		$d = dir($log_path);
		$log_files = array();
		while (false !== ($entry = $d->read())) 
		{
			if($entry == '.' || $entry == '..')
			{
				continue;
			}
			
			$log_files[$entry] = $entry;
		}
		$d->close();
		
		if(count($log_files) == '0')
		{
			echo lang('no_log_files');
			exit;
		}
		
		//we only want the latest log file
		$latest_log = $log_path.end($log_files);
		$f = fopen($latest_log, 'r');
		$lineNo = 0;
		//$startLine = 3;
		//$endLine = 6;
		echo '<div>';
		while ($line = fgets($f)) {
			$lineNo++;
			
			if($lineNo != '1')
			{
				echo $line.'<br />';
			}
			
			if($lineNo == '1000')
			{
				break;
			}
			
			/*
			if ($lineNo >= $startLine) {
				echo $line;
			}
			if ($lineNo == $endLine) {
				break;
			}
			*/
		}
		fclose($f);	

		echo '<div>';
		exit;
	}
	
	public function tag_test()
	{
		$data = array();
		if(count($data) == '0')
		{
			return $this->EE->TMPL->no_results();
		}
				
		$output = $this->prep_output($data);
		return $output;		
	}
}