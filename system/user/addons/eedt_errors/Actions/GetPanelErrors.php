<?php

namespace DebugToolbar\Errors\Actions;

use ExpressionEngine\Service\Addon\Controllers\Action\AbstractRoute;

class GetPanelErrors extends AbstractRoute
{
    public function process()
    {
        $log_data = ee('eedt_errors:LoggerService')->getLogContents();
        $vars = [
            'errors' => $log_data
        ];

        echo ee()->load->view('eedt_errors', $vars, true);
        exit;
    }
}
