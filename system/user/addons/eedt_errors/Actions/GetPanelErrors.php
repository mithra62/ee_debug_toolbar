<?php

namespace DebugToolbar\Errors\Actions;

use DebugToolbar\Actions\AbstractAction;

class GetPanelErrors extends AbstractAction
{
    public function processDebug()
    {
        if (!ee('eedt:ToolbarService')->canViewToolbar()) {
            return;
        }

        $log_data = ee('eedt_errors:LoggerService')->getLogContents();
        $toolbar = ee('eedt:ToolbarService');
        $vars = [
            'errors' => $log_data,
            'clear_logs_url' => ee()->config->site_url() . '?ACT=' . $toolbar->fetchActionId('ClearErrorLog', $class = 'Eedt_errors'),
        ];

        echo ee()->load->view('eedt_errors', $vars, true);
        exit;
    }
}
