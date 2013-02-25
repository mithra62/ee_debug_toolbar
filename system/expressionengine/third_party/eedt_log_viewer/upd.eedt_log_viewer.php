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
 * EE Debug Toolbar - Memory History Updater Class
 *
 * Updater class
 *
 * @package        mithra62:EE_debug_toolbar
 * @author         Eric Lamb
 * @filesource     ./system/expressionengine/third_party/eedt_log_viewer/upd.eedt_log_viewer.php
 */
class Eedt_log_viewer_upd 
{     
    public $class = '';
    
    public $settings_table = '';  
     
    public function __construct() 
    { 
		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();
		
		$path = dirname(realpath(__FILE__));
		include $path.'/config'.EXT;
		$this->class = $config['class_name'];
		$this->version = $config['version'];	
		$this->ext_class_name = $config['ext_class_name'];	
    } 
    
	public function install() 
	{
		$this->EE->load->dbforge();
	
		$data = array(
			'module_name' => $this->class,
			'module_version' => $this->version,
			'has_cp_backend' => 'y',
			'has_publish_fields' => 'n'
		);
	
		$this->EE->db->insert('modules', $data);
		
		$sql = "INSERT INTO exp_actions (class, method) VALUES ('".$this->class."', 'get_panel_logs')";
		$this->EE->db->query($sql);
		
		$this->activate_extension();
		
		return TRUE;
	} 
	
	public function activate_extension()
	{
		$data = array();
		
		$data[] = array(
				'class'     => $this->ext_class_name,
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

	public function uninstall()
	{
		$this->EE->load->dbforge();
	
		$this->EE->db->select('module_id');
		$query = $this->EE->db->get_where('modules', array('module_name' => $this->class));
	
		$this->EE->db->where('module_id', $query->row('module_id'));
		$this->EE->db->delete('module_member_groups');
	
		$this->EE->db->where('module_name', $this->class);
		$this->EE->db->delete('modules');
	
		$this->EE->db->where('class', $this->class);
		$this->EE->db->delete('actions');
		
		$this->disable_extension();
	
		return TRUE;
	}
	
	public function disable_extension()
	{
		$this->EE->db->where('class', $this->ext_class_name);
		$this->EE->db->delete('extensions');
	}

	public function update($current = '')
	{
		
		if ($current == $this->version)
		{
			return FALSE;
		}
	}	
}