<?php
namespace Mithra62\DebugToolbar\Panels;

class Database extends AbstractPanel
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
        $view->add_css($this->toolbar->create_theme_url('default', 'css') . '/ee_debug_panel_database.css');

        return $view;
    }
}
