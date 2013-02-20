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
 * EE Debug Toolbar - Performance Alerts Extension
 *
 * Extension class
 *
 * @package        mithra62:EE_debug_toolbar
 * @author         Eric Lamb
 * @filesource     ./system/expressionengine/third_party/eedt_perf_alerts/ext.eedt_perf_alerts.php
 */
class Eedt_perf_alerts_ext
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
	
	/**
	 * The maximum length the combined SQL execution should take
	 * @var float
	 */
	public $max_sql_time = 0.1;
	
	/**
	 * The maximum number of queries acceptable
	 * @var int
	 */
	public $max_queries = 100;
	
	/**
	 * How many MBs of memory that's acceptable
	 * @var float
	 */
	public $max_memory = 10;
	
	/**
	 * How many seconds are acceptable for page execution
	 * @var float
	 */
	public $max_time = 0.5;

	public function __construct($settings = '')
	{
		$this->EE       =& get_instance();
		$this->EE->lang->loadfile('eedt_perf_alerts');
		$this->name        = lang('eedt_perf_alerts_module_name');
		$this->description = lang('eedt_perf_alerts_module_description');
		$this->EE->load->add_package_path(PATH_THIRD . 'ee_debug_toolbar/');
		$this->EE->load->add_package_path(PATH_THIRD . 'eedt_perf_alerts/');
	}
	
	public function ee_debug_toolbar_modify_output($view)
	{
		$view = ($this->EE->extensions->last_call != '' ? $this->EE->extensions->last_call : $view);
		if($view['elapsed_time'] > $this->max_time)
		{
			$view['panel_data']['time']['class'] = 'flash';
		}
		
		if($view['query_count'] > $this->max_queries)
		{
			$view['panel_data']['db']['class'] = 'flash';
		}
		
		if($view['memory_usage'] > $this->max_memory)
		{
			$view['panel_data']['memory']['class'] = 'flash';
		}
		
		return $view;
	}

	public function activate_extension()
	{			
		$data = array(
				'class'     => __CLASS__,
				'method'    => 'ee_debug_toolbar_modify_output',
				'hook'      => 'ee_debug_toolbar_modify_output',
				'settings'  => '',
				'priority'  => 1,
				'version'   => $this->version,
				'enabled'   => 'y'
		);
		
		$this->EE->db->insert('extensions', $data);		
		return TRUE;
	}
	
	public function update_extension($current = '')
	{
	    if ($current == '' OR $current == $this->version)
	    {
	        return FALSE;
	    }
	
	    $this->EE->db->where('class', __CLASS__);
	    $this->EE->db->update(
	                'extensions',
	                array('version' => $this->version)
	    );
	}
	
	public function disable_extension()
	{
	    $this->EE->db->where('class', __CLASS__);
	    $this->EE->db->delete('extensions');
	}

}