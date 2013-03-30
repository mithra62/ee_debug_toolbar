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
class Eedt_minimal_ext
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
	public $version = '1.0';
	
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
	public $docs_url = 'https://github.com/mithra62/ee_debug_toolbar/wiki/Minimal';	

	public function __construct($settings = '')
	{
		$this->EE       =& get_instance();
		$this->EE->lang->loadfile('eedt_minimal');
		$this->name        = lang('eedt_minimal_module_name');
		$this->description = lang('eedt_minimal_module_description');
		$this->EE->load->add_package_path(PATH_THIRD . 'ee_debug_toolbar/');
		$this->EE->load->add_package_path(PATH_THIRD . 'eedt_minimal/');
	}
	
	/**
	 * Takes the $panels data and converts it into an icon only display
	 * @param array $panels
	 * @return array
	 */
	public function ee_debug_toolbar_mod_panel(array $panels, array $view = array())
	{
		$this->EE->benchmark->mark('eedt_minimal_start');
		
		$panels = ($this->EE->extensions->last_call != '' ? $this->EE->extensions->last_call : $panels);

		foreach($panels AS $key => $value)
		{
			$panels[$key]->set_button_icon_alt_text($panels[$key]->get_name());
			$panels[$key]->set_button_label('');
		}

		$theme_css_url = eedt_theme_url().'eedt_minimal/css/';
		$panels[$key]->add_css($theme_css_url.'minimal.css', TRUE);
		
		$this->EE->benchmark->mark('eedt_minimal_end');
		return $panels;
	}

	public function activate_extension()
	{			
		$data = array(
				'class'     => __CLASS__,
				'method'    => 'ee_debug_toolbar_mod_panel',
				'hook'      => 'ee_debug_toolbar_mod_panel',
				'settings'  => '',
				'priority'  => 99999999,
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