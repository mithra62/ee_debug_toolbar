<?php
namespace Mithra62\DebugToolbar\Panels;

class Time extends AbstractPanel
{
    /**
     * @var string
     */
    protected string $name = "time";

    public function __construct()
    {
        parent::__construct();
        $this->button_label = ee()->benchmark->elapsed_time('total_execution_time_start', 'total_execution_time_end') . 's';
    }

    /**
     * @param Model $view
     * @return Model
     */
    public function addPanel(Model $view): Model
    {
        $view = parent::addPanel($view);
        $view->addCss($this->toolbar->createThemeUrl('default', 'css') . '/ee_debug_panel_time.css');
        return $view;
    }
}
