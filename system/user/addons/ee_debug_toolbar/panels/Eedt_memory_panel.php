<?php
/**
 * Memory Panel
 *
 * @author Christopher Imrie
 */
require_once PATH_THIRD . "ee_debug_toolbar/classes/Eedt_base_panel.php";

class Eedt_memory_panel extends Eedt_base_panel
{
	protected $name = "memory";
	
	public function __construct()
	{
		parent::__construct();
		$this->button_label = $this->EE->toolbar->filesize_format(memory_get_peak_usage()).' '.ini_get('memory_limit');
	}


	public function ee_debug_toolbar_add_panel($view)
	{
		$view->set_name($this->name);
		$view->set_button_label($this->button_label);
		$view->set_button_icon($this->button_icon_uri);
		$view->set_panel_contents($this->EE->load->view('partials/memory', array(), TRUE));
		$view->add_css( $this->EE->toolbar->create_theme_url('default', 'css').'/ee_debug_panel_memory.css');
		$view->add_js( $this->EE->toolbar->create_theme_url('default', 'js').'/ee_debug_panel_memory.js');
	
		return $view;
	}	
}
