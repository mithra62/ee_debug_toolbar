<?php
/**
 * Database Panel
 *
 * @author Christopher Imrie
 */
require_once PATH_THIRD . "ee_debug_toolbar/classes/Eedt_base_panel.php";

class Eedt_database_panel extends Eedt_base_panel
{
	protected $name = "database";

	public function __construct()
	{
		parent::__construct();
		$query_data = $this->EE->toolbar->setup_queries();
		$this->button_label = $this->EE->db->query_count.' '.lang('eedt_in').' '.$query_data['total_time'].'s';
	}

	public function ee_debug_toolbar_add_panel($view)
	{
		$view = parent::ee_debug_toolbar_add_panel($view);
		$view->add_css( $this->EE->toolbar->create_theme_url('default', 'css').'/ee_debug_panel_database.css');

		return $view;
	}
}
