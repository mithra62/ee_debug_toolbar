<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * mithra62 - EE Debug Toolbar
 *
 * @package        mithra62:EE_debug_toolbar
 * @author         Eric Lamb
 * @copyright      Copyright (c) 2013, mithra62, Eric Lamb.
 * @link           http://mithra62.com/
 * @updated        1.0
 * @filesource     ./system/expressionengine/third_party/ee_debug_toolbar/
 */

/**
 * EE Debug Toolbar - Updater Class
 *
 * Updater class
 *
 * @package        mithra62:EE_debug_toolbar
 * @author         Eric Lamb
 * @filesource     ./system/expressionengine/third_party/ee_debug_toolbar/upd.ee_debug_toolbar.php
 */
class Ee_debug_toolbar_upd 
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

		$this->EE->lang->loadfile('ee_debug_toolbar');
		$this->EE->load->add_package_path(PATH_THIRD . 'ee_debug_toolbar/');
		$this->EE->load->model('ee_debug_settings_model', 'debug_settings');
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
		
		$sql = "INSERT INTO exp_actions (class, method) VALUES ('".$this->class."', 'act')";
		$this->EE->db->query($sql);
		
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
	
		$this->EE->db->insert('extensions', $data);
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
		$this->EE->dbforge->drop_table($this->EE->debug_settings->settings_table);
		
		$cache_dir = APPPATH.'cache/eedt/';
		if(is_dir($cache_dir))
		{
			$this->EE->load->helpers('file');
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
		$this->EE->load->dbforge();
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

	private function add_settings_table()
	{
		$this->EE->load->dbforge();
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
	
		$this->EE->dbforge->add_field($fields);
		$this->EE->dbforge->add_key('id', TRUE);
		$this->EE->dbforge->create_table($this->EE->debug_settings->settings_table, TRUE);
	}	
}