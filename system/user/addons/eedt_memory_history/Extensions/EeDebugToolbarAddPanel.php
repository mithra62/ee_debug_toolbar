<?php

namespace DebugToolbar\MemoryHistory\Extensions;

use DebugToolbar\Panels\Model;

class EeDebugToolbarAddPanel extends AbstractHook
{
    public function process(array $panels = [], array $vars = []): array
    {
        ee()->benchmark->mark('eedt_memory_history_start');
        $panels = (ee()->extensions->last_call != '' ? ee()->extensions->last_call : $panels);
        $settings = $this->toolbar->getSettings();

        $panels['memory_history'] = new Model();
        $panels['memory_history']->setName("memory_history");
        $panels['memory_history']->setPanelContents(ee()->load->view('memory_history', array('position' => $settings['memory_history_position']), true));
        $panels['memory_history']->addJs('https://www.google.com/jsapi', true);
        $panels['memory_history']->addJs(ee('eedt:OutputService')->themeUrl() . 'eedt_memory_history/js/memory_history.js', true);
        $panels['memory_history']->addCss(ee('eedt:OutputService')->themeUrl() . 'eedt_memory_history/css/memory_history.css', true);
        $panels['memory_history']->setInjectionPoint(Model::PANEL_AFTER_TOOLBAR);

        $this->trackMemoryAndSqlUsage($vars);

        ee()->benchmark->mark('eedt_memory_history_end');
        return $panels;
    }

    protected function trackMemoryAndSqlUsage($vars)
    {
        $log = ee('Database')->getLog();
        $data = [
            'session_id' => ee()->session->userdata['session_id'],
            'url' => $_SERVER["REQUEST_URI"] . $_SERVER["QUERY_STRING"],
            'peak_memory' => (float)$vars['memory_usage'],
            'sql_count' => $log->getQueryCount(),//$vars['query_count'],
            'execution_time' => $vars['elapsed_time'],
            'timestamp' => ee()->localize->now,
            'cp' => ee()->input->get('D') == 'cp' ? 'y' : 'n'
        ];
        ee()->db->insert('eedt_memory_history', $data);
    }
}
