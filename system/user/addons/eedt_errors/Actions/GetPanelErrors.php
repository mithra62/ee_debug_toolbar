<?php

namespace DebugToolbar\Errors\Actions;

use ExpressionEngine\Service\Addon\Controllers\Action\AbstractRoute;

class GetPanelErrors extends AbstractRoute
{
    public function process()
    {
        if (!ee('ee_debug_toolbar:ToolbarService')->canViewToolbar()) {
            return;
        }

        $log_data = ee('eedt_errors:LoggerService')->getLogContents();
        $toolbar = ee('ee_debug_toolbar:ToolbarService');
        $vars = [
            'errors' => $log_data,
            'clear_logs_url' => ee()->config->site_url() . '?ACT=' . $toolbar->fetchActionId('ClearErrorLog', $class = 'Eedt_errors'),
        ];

        echo ee()->load->view('eedt_errors', $vars, true);
        exit;
    }
}
