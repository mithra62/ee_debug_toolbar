<?php

namespace DebugToolbar\PerfAlerts\Extensions;

class EeDebugToolbarModPanel extends AbstractHook
{
    public function process(array $panels, array $view = [])
    {
        ee()->benchmark->mark('eedt_performance_alerts_start');
        $panels = (ee()->extensions->last_call != '' ? ee()->extensions->last_call : $panels);
        $settings = $this->toolbar->getSettings();

        $view['settings'] = $settings;

        //check total time
        if ($view['elapsed_time'] > $settings['max_exec_time']) {
            $panels['time']->setPanelCssClass('flash');
        }

        //make sure we're not running too many queries
        if ($view['query_count'] > $settings['max_queries']) {
            $panels['database']->setPanelCssClass('flash');
        }

        //and how long did those queries take?
        if (ee('Database')->currentExecutionTime() > $settings['max_sql_time']) {
            $panels['database']->setPanelCssClass('flash');
        }

        //is memory usage bad?
        if ($view['memory_usage'] > $settings['max_memory']) {
            $panels['memory']->setPanelCssClass('flash');
        }

        $view['perf_theme_img_url'] = eedt_theme_url() . 'eedt_perf_alerts/images/';
        $view['perf_theme_js_url'] = eedt_theme_url() . 'eedt_perf_alerts/js/';
        $view['perf_theme_css_url'] = eedt_theme_url() . 'eedt_perf_alerts/css/';

        $panels['database']->setPanelContents(ee()->load->view('db', $view, true));
        $panels['database']->addJs($view['perf_theme_js_url'] . 'perf_alerts.js');

        ee()->benchmark->mark('eedt_performance_alerts_end');

        return $panels;
    }
}
