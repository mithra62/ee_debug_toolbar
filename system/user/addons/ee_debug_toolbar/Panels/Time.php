<?php
namespace Mithra62\DebugToolbar\Panels;

class Time extends AbstractPanel
{
    protected $name = "time";

    public function __construct()
    {
        parent::__construct();
        $this->button_label = ee()->benchmark->elapsed_time('total_execution_time_start', 'total_execution_time_end') . 's';
    }

    public function ee_debug_toolbar_add_panel($view)
    {
        $view = parent::ee_debug_toolbar_add_panel($view);
        $view->add_css($this->toolbar->createThemeUrl('default', 'css') . '/ee_debug_panel_time.css');

        return $view;
    }
}
