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

    public $actions = [
        [
            'class' => 'Ee_debug_toolbar',
            'method' => 'act', // required
            'csrf_exempt' => true
        ]
    ];

    public $methods = [
        [
            'method' => 'toolbar',
            'hook' => 'sessions_end',
            'priority' => 9999999,
            'enabled' => 'y'
        ]
    ];

    public function __construct() 
    {
        parent::__construct();
    } 
    
	public function install() 
	{
	    if(parent::install()) {
	        $this->addSettingsTable();
	        $this->activate_extension();
	        return true;
        }
	}

	public function uninstall()
	{
	    if(parent::uninstall()) {
	        $this->disable_extension();
            ee()->load->dbforge();
            ee()->dbforge->drop_table('ee_debug_toolbar_settings');

            $cache_dir = APPPATH.'cache/eedt/';
            if(is_dir($cache_dir)) {
                ee()->load->helpers('file');
                delete_files($cache_dir, true);
                if(is_dir($cache_dir)) {
                    rmdir($cache_dir);
                }
            }

            return true;
        }
	}

	public function update($current = '')
	{
		if ($current == $this->version) {
			return FALSE;
		}
	}

	private function addSettingsTable()
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
		ee()->dbforge->create_table('ee_debug_toolbar_settings', TRUE);
	}	
}