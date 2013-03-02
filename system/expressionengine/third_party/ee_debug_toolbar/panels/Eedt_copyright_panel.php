<?php
/**
 * Copyright Panel
 *
 * @author Christopher Imrie
 */
require_once PATH_THIRD . "ee_debug_toolbar/classes/Eedt_base_panel.php";

class Eedt_copyright_panel extends Eedt_base_panel
{
	protected $name = "copyright";
	
	public function __construct()
	{
		parent::__construct();
		$this->button_label = 'v'.APP_VER.' / '.phpversion();
	}
}
