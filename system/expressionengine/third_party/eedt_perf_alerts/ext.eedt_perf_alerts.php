<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * mithra62 - EE Debug Toolbar
 *
 * @package        mithra62:EE_debug_toolbar
 * @author         Eric Lamb
 * @copyright      Copyright (c) 2012, mithra62, Eric Lamb.
 * @link           http://mithra62.com/
 * @updated        1.0
 * @filesource     ./system/expressionengine/third_party/eedt_perf_alerts/
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
	
	public $default_settings = array(
			'max_exec_time' => 0.5,
			'max_memory' => 10,
			'max_queries' => 100,
			'max_sql_time' => 0.1
	);

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
		$this->EE->benchmark->mark('eedt_performance_alerts_start');
		$view = ($this->EE->extensions->last_call != '' ? $this->EE->extensions->last_call : $view);
		$settings = $this->EE->toolbar->get_settings();
		
		//check total time
		if($view['elapsed_time'] > $settings['max_exec_time'])
		{
			$view['panel_data']['time']['class'] = 'flash';
		}
		
		//make sure we're not running too many queries
		if($view['query_count'] > $settings['max_queries'])
		{
			$view['panel_data']['db']['class'] = 'flash';
		}
		
		//and how long did those queries take?
		if($view['query_data']['total_time'] > $settings['max_sql_time'])
		{
			$view['panel_data']['db']['class'] = 'flash';
		}
		
		//is memory usage bad?
		if($view['memory_usage'] > $settings['max_memory'])
		{
			$view['panel_data']['memory']['class'] = 'flash';
		}
		
		$this->EE->benchmark->mark('eedt_performance_alerts_end');
		return $view;
	}
	
	public function ee_debug_toolbar_init_settings($default_settings)
	{
		$default_settings = ($this->EE->extensions->last_call != '' ? $this->EE->extensions->last_call : $default_settings);
		return array_merge($default_settings, $this->default_settings);
	}
	
	public function ee_debug_toolbar_settings_form()
	{
		$settings = $this->EE->toolbar->get_settings();
		$settings_disable = FALSE;
		if(isset($this->EE->config->config['ee_debug_toolbar']))
		{
			$settings_disable = 'disabled="disabled"';
		}		
		$this->EE->table->add_row('<label for="max_exec_time">'.lang('max_exec_time').'</label><div class="subtext">'.lang('max_exec_time_instructions').'</div>', form_input('max_exec_time',  $settings['max_exec_time'], 'id="max_exec_time"'. $settings_disable));
		$this->EE->table->add_row('<label for="max_memory">'.lang('max_memory').'</label><div class="subtext">'.lang('max_memory_instructions').'</div>', form_input('max_memory',  $settings['max_memory'], 'id="max_memory"'. $settings_disable));
		$this->EE->table->add_row('<label for="max_queries">'.lang('max_queries').'</label><div class="subtext">'.lang('max_queries_instructions').'</div>', form_input('max_queries',  $settings['max_queries'], 'id="max_queries"'. $settings_disable));
		$this->EE->table->add_row('<label for="max_sql_time">'.lang('max_sql_time').'</label><div class="subtext">'.lang('max_sql_time_instructions').'</div>', form_input('max_sql_time',  $settings['max_sql_time'], 'id="max_sql_time"'. $settings_disable));
		
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

		$data = array(
				'class'     => __CLASS__,
				'method'    => 'ee_debug_toolbar_settings_form',
				'hook'      => 'ee_debug_toolbar_settings_form',
				'settings'  => '',
				'priority'  => 1,
				'version'   => $this->version,
				'enabled'   => 'y'
		);
		
		$this->EE->db->insert('extensions', $data);	
		
		$this->EE->db->insert('extensions', $data);
		
		$data = array(
				'class'     => __CLASS__,
				'method'    => 'ee_debug_toolbar_init_settings',
				'hook'      => 'ee_debug_toolbar_init_settings',
				'settings'  => '',
				'priority'  => 5,
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