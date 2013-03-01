<?php
/**
 * Config Panel
 *
 * @author Christopher Imrie
 */
require_once PATH_THIRD . "ee_debug_toolbar/classes/Eedt_base_panel.php";

class Eedt_config_panel extends Eedt_base_panel
{
	protected $name = "config";
	
	public function __construct()
	{
		parent::__construct();
		$this->button_label = lang($this->name).' ('.count($this->EE->config->config).')';
	}
}
