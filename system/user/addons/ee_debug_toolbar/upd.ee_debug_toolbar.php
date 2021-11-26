<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

use ExpressionEngine\Service\Addon\Installer;

class Ee_debug_toolbar_upd extends Installer
{
    public $has_cp_backend = 'y';
    public $has_publish_fields = 'n';

    public $class = '';

    public $settings_table = '';
     
    public function __construct() 
    {
		$this->class = 'Ee_debug_toolbar';
		$this->version = DEBUG_TOOLBAR_VERSION;
		$this->ext_class_name = 'Ee_debug_toolbar_ext';

		ee()->lang->loadfile('ee_debug_toolbar');
		ee()->load->add_package_path(PATH_THIRD . 'ee_debug_toolbar/');
		ee()->load->model('ee_debug_settings_model', 'debug_settings');
    } 
    
	public function install() 
	{
		ee()->load->dbforge();
	
		$data = array(
			'module_name' => $this->class,
			'module_version' => $this->version,
			'has_cp_backend' => 'y',
			'has_publish_fields' => 'n'
		);
	
		ee()->db->insert('modules', $data);
		
		$sql = "INSERT INTO exp_actions (class, method) VALUES ('".$this->class."', 'act')";
		ee()->db->query($sql);
		
		$this->add_settings_table();
		$this->activate_extension();
		
		return TRUE;
	} 
	
	public function activate_extension()
	{
		$data = array(
				'class'    => $this->ext_class_name,
				'method'   => 'toolbar',
				'hook'     => 'sessions_end',
				'settings' => serialize(array()),
				'priority' => 9999999,
				'version'  => $this->version,
				'enabled'  => 'y'
		);
	
		ee()->db->insert('extensions', $data);
	}

	public function uninstall()
	{
		ee()->load->dbforge();
	
		ee()->db->select('module_id');
		$query = ee()->db->get_where('modules', array('module_name' => $this->class));
	
		ee()->db->where('module_id', $query->row('module_id'));
		ee()->db->delete('module_member_groups');
	
		ee()->db->where('module_name', $this->class);
		ee()->db->delete('modules');
	
		ee()->db->where('class', $this->class);
		ee()->db->delete('actions');
		
		$this->disable_extension();
		ee()->dbforge->drop_table(ee()->debug_settings->settings_table);
		
		$cache_dir = APPPATH.'cache/eedt/';
		if(is_dir($cache_dir))
		{
			ee()->load->helpers('file');
			delete_files($cache_dir, TRUE);
			if(is_dir($cache_dir))
			{
				rmdir($cache_dir);
			}
		}
	
		return TRUE;
	}
	
	public function disable_extension()
	{
		ee()->load->dbforge();
		ee()->db->where('class', $this->ext_class_name);
		ee()->db->delete('extensions');			
	}

	public function update($current = '')
	{
		if ($current == $this->version)
		{
			return FALSE;
		}
	}

	private function add_settings_table()
	{
		ee()->load->dbforge();
		$fields = array(
				'id'	=> array(
						'type'			=> 'int',
						'constraint'	=> 10,
						'unsigned'		=> TRUE,
						'null'			=> FALSE,
						'auto_increment'=> TRUE
				),
				'setting_key'	=> array(
						'type' 			=> 'varchar',
						'constraint'	=> '30',
						'null'			=> FALSE,
						'default'		=> ''
				),
				'setting_value'  => array(
						'type' 			=> 'text',
						'null'			=> FALSE
				),
				'serialized' => array(
						'type' => 'int',
						'constraint' => 1,
						'null' => TRUE,
						'default' => '0'
				)
		);
	
		ee()->dbforge->add_field($fields);
		ee()->dbforge->add_key('id', TRUE);
		ee()->dbforge->create_table(ee()->debug_settings->settings_table, TRUE);
	}	
}