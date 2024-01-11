<?php
namespace DebugToolbar\Panels;

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
        $settings = $this->toolbar->getSettings();
        $data['settings'] = $settings;
        $view->addCss($this->toolbar->createThemeUrl('default', 'css') . '/ee_debug_panel_database.css');
        $view->addJs($this->toolbar->createThemeUrl('default', 'js') . '/ee_debug_panel_database.js');
        $view->setPanelContents(ee()->load->view('partials/database', $data, true));

        $log = ee('Database')->getLog();
        if ($log->getQueryCount() > $settings['max_queries']) {
            $view->setPanelCssClass('flash');
        }

        if (ee('Database')->currentExecutionTime() > $settings['max_sql_time']) {
            $view->setPanelCssClass('flash');
        }

        return $view;
    }
}
