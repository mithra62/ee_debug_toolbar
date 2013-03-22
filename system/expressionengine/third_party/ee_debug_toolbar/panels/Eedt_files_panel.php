<?php
/**
 * Files Panel
 *
 * @author Christopher Imrie
 */
require_once PATH_THIRD . "ee_debug_toolbar/classes/Eedt_base_panel.php";

class Eedt_files_panel extends Eedt_base_panel
{
	protected $name = "files";

	public function __construct()
	{
		parent::__construct();
		$this->button_label = count(get_included_files()).' '.lang('files');
	}	
}
