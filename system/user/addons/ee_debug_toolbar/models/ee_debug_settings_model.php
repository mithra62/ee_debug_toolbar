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
 * EE Debug Toolbar - Settings
 *
 * Settings Model
 *
 * @package        mithra62:EE_debug_toolbar
 * @author         Eric Lamb
 * @filesource     ./system/expressionengine/third_party/ee_debug_toolbar/models/ee_debug_settings_model.php
 */
class Ee_debug_settings_model extends CI_Model
{
	/**
	 * The name of the settings table
	 *
	 * @var string
	 */
	public $settings_table = 'ee_debug_toolbar_settings';
	
	/**
	 * The default settings to use if none are found
	 * @var array
	 */
	public $_defaults = array(
				'theme' => 'default',
				'toolbar_position' => 'bottom-left',
				'cache_path' => '',
				'profile_exts' => array(
					'js',
					'css',
					'jpg',
					'jpeg',
					'gif',
					'png',
					'bmp',
					'pdf',
					'svg',
					'htm',
					'html',
					'xhtml',
					'csv',
					'rss',
					'atom',
					'xml'
				)
	);
	
	/**
	 * The key names for values that should be serialized. 
	 * @var array
	 */
	private $_serialized = array(
			
	);
	
	/**
	 * Which fields should be encrypted before storage
	 * @var array
	 */
	private $_encrypted = array(

	);	
	
	public function __construct()
	{
		parent::__construct();
		$this->_table = $this->settings_table;
		$this->_defaults['cache_path'] = APPPATH.'cache/eedt/';
	}
	
	/**
	 * Adds a setting to the databse
	 * @param string $setting
	 */
	public function add_setting($setting)
	{
		$data = array(
		   'setting_key' => $setting,
		   'setting_value' => ''
		);
		
		return $this->db->insert($this->_table, $data); 
	}	
	
	/**
	 * Allows for dynamic additions to the default settings
	 * @param array $new_defaults
	 */
	public function set_defaults(array $new_defaults = array())
	{
		foreach($new_defaults AS $key => $value)
		{
			$this->_defaults[$key] = $value;
		}
	}
	
	public function get_settings()
	{
		$this->db->flush_cache();
		$this->db->select('setting_key, setting_value, `serialized`');
		$query = $this->db->get($this->_table);	
		$_settings = $query->result_array();
		$settings = array();	
		foreach($_settings AS $setting)
		{
			$settings[$setting['setting_key']] = ($setting['serialized'] == '1' ? unserialize($setting['setting_value']) : $setting['setting_value']);
		}	
		
		//now check to make sure they're all there and set default values if not
		foreach ($this->_defaults as $key => $value)
		{
			//setup the override check
			if(isset($this->config->config['ee_debug_toolbar'][$key]))
			{
				$settings[$key] = $this->config->config['ee_debug_toolbar'][$key];
			}
			
			//normal default check				
			if(!isset($settings[$key]))
			{
				$settings[$key] = $value;
			}
		}

		return $settings;
	}
	
	/**
	 * Returns the value straigt from the database
	 * @param string $setting
	 */
	public function get_setting($setting)
	{
		return $this->db->get_where($this->_table, array('setting_key' => $setting))->result_array();
	}	
	
	/**
	 * Updates EEDT settings
	 * @param array $data
	 * @return boolean
	 */
	public function update_settings(array $data)
	{
		$this->load->library('encrypt');
		foreach($data AS $key => $value)
		{
			
			if(in_array($key, $this->_serialized))
			{
				$value = explode("\n", $value);			
			}
			
			if(in_array($key, $this->_encrypted) && $value != '')
			{
				$value = $this->encrypt->encode($value);
			}
			
			$this->update_setting($key, $value);
		}
		
		return TRUE;
	}
	
	/**
	 * Updates the value of a setting
	 * @param string $key
	 * @param string $value
	 */
	public function update_setting($key, $value)
	{
		if(!$this->_check_setting($key))
		{
			return FALSE;
		}

		$data = array();
		if(is_array($value))
		{
			$value = serialize($value);
			$data['serialized '] = '1';
		}
		
		$data['setting_value'] = $value;
		$this->db->where('setting_key', $key);
		$this->db->update($this->_table, $data);
		
	}

	/**
	 * Verifies that a submitted setting is valid and exists. If it's valid but doesn't exist it is created.
	 * @param string $setting
	 */
	private function _check_setting($setting)
	{
		if(array_key_exists($setting, $this->_defaults))
		{
			if(!$this->get_setting($setting))
			{
				$this->add_setting($setting);
			}
			
			return TRUE;
		}		
	}
	
}