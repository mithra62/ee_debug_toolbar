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
        $log = ee('Database')->getLog();
        $this->button_label = $log->getQueryCount() . ' ' . lang('eedt_in') . ' ' . number_format(ee('Database')->currentExecutionTime(), 4) . 's';
    }

    public function ee_debug_toolbar_add_panel($view)
    {
        $view = parent::ee_debug_toolbar_add_panel($view);
        $view->add_css(ee()->toolbar->create_theme_url('default', 'css') . '/ee_debug_panel_database.css');

        return $view;
    }
}
