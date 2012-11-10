<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

 /**
 * mithra62 - EE Debug Toolbar
 *
 * @package		mithra62:EE_debug_toolbar
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2012, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @updated		1.0
 * @filesource 	./system/expressionengine/third_party/nagger/
 */
 
 /**
 * EE Debug Toolbar - Extension
 *
 * Extension class
 *
 * @package 	mithra62:EE_debug_toolbar
 * @author		Eric Lamb
 * @filesource 	./system/expressionengine/third_party/ee_debug_toolbar/ext.ee_debug_toolbar.php
 */
class Ee_debug_toolbar_ext 
{
	/**
	 * The extensions default settings
	 * @var array
	 */
	public $settings = array();
	
	/**
	 * The extension name
	 * @var string
	 */
	public $name = 'EE Debug Toolbar';
	
	/**
	 * The extension version
	 * @var float
	 */
	public $version = '0.7';
	public $description	= 'Adds an unobtrusive interface for debugging output';
	public $settings_exist	= 'n';
	public $docs_url		= '';
	
	/**
	 * The breadcrumb override
	 * @var array
	 */
	protected static $_breadcrumbs = array();	
		
	public function __construct($settings='')
	{
		$this->EE =& get_instance();
		$this->settings = (!$settings ? $this->settings : $settings);
		$this->EE->lang->loadfile('ee_debug_toolbar');
		$this->EE->load->add_package_path(PATH_THIRD.'ee_debug_toolbar/');
	}
	
	public function toolbar($session)
	{	
		$session = ($this->EE->extensions->last_call != '' ? $this->EE->extensions->last_call : $session);
		if($this->EE->config->config['show_profiler'] != 'y' || $session->userdata('group_id') != '1')
		{
			return $session;
		}
		
		if($this->EE->input->get("C") == "javascript"){
			return $session;
		}		
		
		//return;
		global $EXT;
		
		//Load our modified core Hooks class
		$this->EE->load->file(PATH_THIRD . "ee_debug_toolbar/libraries/Ee_toolbar_hook.php");
		
		//STOLEN FROM CHRIS IMRIE AND REQUIREJS
		
		//We overwrite the CI_Hooks class with our own since the CI_Hooks class will always load
		//hooks class files relative to APPPATH, when what we really need is to load RequireJS hook from the
		//third_party folder, which we KNOW can always be found with PATH_THIRD. Hence we extend the class and
		//simply redefine the _run_hook method to load relative to PATH_THIRD. Simples.
		$EET_EXT = new Ee_toolbar_hooks();
		
		//Capture existing hooks just in case (although this is EE - it's unlikely)
		$EET_EXT->hooks = $EXT->hooks;
		
		//Enable CI Hooks
		$EET_EXT->enabled = TRUE;
		
		//Create the post_controller hook array if needed
		if(!isset($EET_EXT->hooks['post_controller'])){
			$EET_EXT->hooks['post_controller'] = array();
		}
		
		//Add our hook
		$EET_EXT->hooks['post_controller'][] = array(
				'class' => __CLASS__,
				'function' => 'modify_output',
				'filename' => basename(__FILE__),
				'filepath' => "ee_debug_toolbar" ,
				'params' => array()
		);
		
		//Overwrite the global CI_Hooks instance with our modified version
		$EXT = $EET_EXT;
	
		return $session;
	}
	
	/**
	 * Post EE Controller
	 *
	 * This method will be called after the EE Controller has finished.
	 *
	 * @return null
	 */
	public function modify_output()
	{
		//Fetch the final CP Output
		$html = $this->EE->output->final_output;
		$this->EE->load->library('Toolbar');
		$vars = array();
		$vars['query_count'] = $this->EE->db->query_count;
		$vars['elapsed_time'] = $this->EE->benchmark->elapsed_time('total_execution_time_start', 'total_execution_time_end');
		$vars['config_data'] = $this->EE->config->config;
		$vars['benchmark_data'] = $this->EE->toolbar->setup_benchmarks();
		$vars['session_data'] = $this->EE->session->all_userdata();
		$vars['query_data'] = $this->EE->toolbar->setup_queries();
		$vars['memory_usage'] = $this->EE->toolbar->filesize_format(memory_get_peak_usage());
		$vars['template_debugging'] = (isset($this->EE->TMPL->log) ? $this->EE->TMPL->log : FALSE);

		$html = str_replace('</body>', $this->EE->load->view('toolbar', $vars, TRUE).'</body>', $html);

		//Reset the final output to your modified version
		echo $this->EE->output->final_output = $html;
		exit;
	}	
	
	public function settings()
	{
		//echo $this->settings['alert_message'];
		$settings = array();
		$yes_no = array('no' => 'No', 'yes' => 'Yes');

		// Creates a text input with a default value of "EllisLab Brand Butter"
		$settings['enable_publish_form']   = array('s', $yes_no, $this->settings['enable_publish_form']);
		$settings['enable_update_member_group'] = array('s', $yes_no, $this->settings['enable_update_member_group']);
		$settings['enable_file_publish_form'] = array('s', $yes_no, $this->settings['enable_file_publish_form']);
		$settings['enable_channel_add'] = array('s', $yes_no, $this->settings['enable_channel_add']);
		$settings['enable_channel_edit'] = array('s', $yes_no, $this->settings['enable_channel_edit']);
		$settings['alert_message'] = array('t', array('rows' => '5'), $this->settings['alert_message']);				
		
		return $settings;
	}
	
	public function activate_extension()
	{	
		$this->settings['alert_message'] = lang('default_alert_message');			
		$data = array(
				'class'     => __CLASS__,
				'method'    => 'toolbar',
				'hook'      => 'sessions_end',
				'settings'  => serialize($this->settings),
				'priority'  => 187,
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