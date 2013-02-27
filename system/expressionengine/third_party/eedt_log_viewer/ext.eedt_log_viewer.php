<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * mithra62 - EE Debug Toolbar
 *
 * @package        mithra62:EE_debug_toolbar
 * @author         Eric Lamb
 * @copyright      Copyright (c) 2012, mithra62, Eric Lamb.
 * @link           http://mithra62.com/
 * @updated        1.0
 * @filesource     ./system/expressionengine/third_party/eedt_memory_history/
 */

/**
 * EE Debug Toolbar - Memory History Extension
 *
 * Extension class
 *
 * @package        mithra62:EE_debug_toolbar
 * @author         Eric Lamb
 * @filesource     ./system/expressionengine/third_party/eedt_memory_history/ext.eedt_memory_history.php
 */
class Eedt_log_viewer_ext
{
	/**
	 * The extension name
	 *
	 * @var string
	 */
	public $name = '';	

	/**
	 * The extension version
	 *
	 * @var float
	 */
	public $version = '0.1';
	
	/**
	 * Used nowhere and not really needed (ya hear me ElisLab?!?!)
	 * 
	 * @var string
	 */
	public $description = '';
	
	/**
	 * We're doing our own settings now so set this to off.
	 * 
	 * @var string
	 */
	public $settings_exist = 'n';
	
	/**
	 * Where to get help (nowhere for now)
	 * 
	 * @var string
	 */
	public $docs_url = '';
	
	public $eedt_act = array('get_panel_logs');

	public function __construct($settings = '')
	{
		$this->EE       =& get_instance();
		$this->EE->lang->loadfile('eedt_log_viewer');
		$this->name        = lang('eedt_log_viewer_module_name');
		$this->description = lang('eedt_log_viewer_module_description');
		$this->EE->load->add_package_path(PATH_THIRD . 'ee_debug_toolbar/');
		$this->EE->load->add_package_path(PATH_THIRD . 'eedt_log_viewer/');
	}
	
	public function ee_debug_toolbar_modify_output($view)
	{
		$this->EE->benchmark->mark('eedt_log_viewer_start');
		$view = ($this->EE->extensions->last_call != '' ? $this->EE->extensions->last_call : $view);
		
		$vars['logs_enabled'] = FALSE;
		if($this->EE->config->config['log_threshold'] >= 1)
		{
			$vars['logs_enabled'] = TRUE;
		}
		
		$vars['log_dir_writable'] = FALSE;
		if(is_writable($this->EE->config->config['log_path']))
		{
			$vars['log_dir_writable'] = TRUE;
		}
		
		$vars['ajax_action_url'] = $this->EE->toolbar->create_act_url('get_panel_logs', __CLASS__);
		$vars['theme_img_url'] = URL_THIRD_THEMES.'eedt_log_viewer/images/';
		$vars['theme_js_url'] = URL_THIRD_THEMES.'eedt_log_viewer/js/';
		$vars['theme_css_url'] = URL_THIRD_THEMES.'eedt_log_viewer/css/';
		
		$view['panel_data']['log_viewer']['image'] = $vars['theme_img_url'].'logs.png';
		$view['panel_data']['log_viewer']['title'] = lang('log_viewer');
		$view['panel_data']['log_viewer']['data_target'] = 'EEDebug_log_viewer';
		$view['panel_data']['log_viewer']['class'] = '';
		$view['panel_data']['log_viewer']['view_html'] = $this->EE->load->view('log_viewer', $vars, TRUE);		
		
		$this->EE->benchmark->mark('eedt_log_viewer_end');
		return $view;
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

	public function activate_extension()
	{
		$data = array();
		
		$data[] = array(
				'class'     => __CLASS__,
				'method'    => 'ee_debug_toolbar_modify_output',
				'hook'      => 'ee_debug_toolbar_modify_output',
				'settings'  => '',
				'priority'  => 500,
				'version'   => $this->version,
				'enabled'   => 'y'
		);
	
		foreach($data AS $ex)
		{
			$this->EE->db->insert('extensions', $ex);	
		}		
	}
	
	public function disable_extension()
	{
		$this->EE->db->where('class', __CLASS__);
		$this->EE->db->delete('extensions');
	}
		
	public function update_extension($current = '')
	{
		return TRUE;
	}
}