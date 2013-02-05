<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * mithra62 - EE Debug Toolbar
 *
 * @package        mithra62:EE_debug_toolbar
 * @author         Eric Lamb
 * @copyright      Copyright (c) 2012, mithra62, Eric Lamb.
 * @link           http://mithra62.com/
 * @updated        1.0
 * @filesource     ./system/expressionengine/third_party/ee_debug_toolbar/
 */

/**
 * EE Debug Toolbar - Installation Library
 *
 * @package        mithra62:Ee_toolbar_install
 * @author         Eric Lamb
 * @filesource     ./system/expressionengine/third_party/ee_debug_toolbar/libraries/Ee_toolbar_install.php
 */
class Ee_toolbar_install
{
	public function __construct()
	{
		$this->EE = & get_instance();
		$this->EE->load->model('ee_debug_settings_model', 'debug_settings');
	}
	
	/**
	 * Wrapper to install the toolbar
	 * 
	 * Abstraction's for pussies
	 * 
	 * @param str $extension
	 * @param int $version
	 * @return boolean
	 */
	public function install($extension, $version)
	{
		$data                            = array(
				'class'    => $extension,
				'method'   => 'toolbar',
				'hook'     => 'sessions_end',
				'settings' => serialize(array()),
				'priority' => 9999999,
				'version'  => $version,
				'enabled'  => 'y'
		);
	
		$this->EE->db->insert('extensions', $data);
		$this->add_settings_table();
	
		return TRUE;
	}
	
	/**
	 * Wrapper to handle updates
	 * @param str $class
	 * @param int $version
	 */
	public function update($class, $version, $current)
	{
		$this->EE->db->where('class', $class);
		$this->EE->db->update(
				'extensions',
				array('version' => $version)
		);
		
		if (version_compare($current, "0.9", '<'))
		{
			$this->add_settings_table();
		}
	}
	
	/**
	 * Wrapper to remove the extension
	 * @param str $extension
	 */
	public function remove($extension)
	{
		$this->EE->load->dbforge();
		$this->EE->db->where('class', $extension);
		$this->EE->db->delete('extensions');
		
		$this->EE->dbforge->drop_table($this->EE->debug_settings->settings_table);
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