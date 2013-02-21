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
class Eedt_memory_history_ext
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

	public function __construct($settings = '')
	{
		$this->EE       =& get_instance();
		$this->EE->lang->loadfile('eedt_memory_history');
		$this->name        = lang('eedt_memory_history_module_name');
		$this->description = lang('eedt_memory_history_module_description');
		$this->EE->load->add_package_path(PATH_THIRD . 'ee_debug_toolbar/');
		$this->EE->load->add_package_path(PATH_THIRD . 'eedt_memory_history/');
	}
	
	public function ee_debug_toolbar_modify_output($view)
	{
		$this->EE->benchmark->mark('eedt_memory_history_start');
		$view = ($this->EE->extensions->last_call != '' ? $this->EE->extensions->last_call : $view);
		
		$vars['theme_img_url'] = URL_THIRD_THEMES.'eedt_memory_history/images/';
		$vars['theme_js_url'] = URL_THIRD_THEMES.'eedt_memory_history/js/';
		$vars['theme_css_url'] = URL_THIRD_THEMES.'eedt_memory_history/css/';
				
		//$view['panel_data']['memory_history']['view_script'] = 'memory_history';
		$view['panel_data']['memory_history']['image'] = $vars['theme_img_url'].'memory_history.png';
		$view['panel_data']['memory_history']['title'] = lang('memory_history');
		$view['panel_data']['memory_history']['data_target'] = 'EEDebug_memory_history';
		$view['panel_data']['memory_history']['class'] = '';
		$view['panel_data']['memory_history']['view_html'] = $this->EE->load->view('memory_history', $vars, TRUE);

		//unset($view['panel_data']['files']);
		$this->EE->benchmark->mark('eedt_memory_history_end');
		return $view;
	}

	public function activate_extension()
	{			
		$data = array(
				'class'     => __CLASS__,
				'method'    => 'ee_debug_toolbar_modify_output',
				'hook'      => 'ee_debug_toolbar_modify_output',
				'settings'  => '',
				'priority'  => 49,
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