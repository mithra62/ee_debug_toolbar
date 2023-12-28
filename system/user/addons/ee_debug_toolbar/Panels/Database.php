<?php
namespace Mithra62\DebugToolbar\Panels;

class Database extends AbstractPanel
{
    /**
     * @var string
     */
    protected string $name = "database";

    public function __construct()
    {
        parent::__construct();
        $log = ee('Database')->getLog();
        $this->button_label = $log->getQueryCount() . ' ' . lang('eedt_in') . ' ' . number_format(ee('Database')->currentExecutionTime(), 4) . 's';
    }

    /**
     * @param $view
     * @return mixed
     */
    public function addPanel(Model $view): Model
    {
        $view = parent::addPanel($view);
        $view->addCss($this->toolbar->createThemeUrl('default', 'css') . '/ee_debug_panel_database.css');

        return $view;
    }
}
