<?php

namespace DebugToolbar\Errors\Actions;

use ExpressionEngine\Service\Addon\Controllers\Action\AbstractRoute;

class ClearErrorLog extends AbstractRoute
{
    public function process()
    {
        if (!ee('eedt:ToolbarService')->canViewToolbar()) {
            return;
        }

        echo 'fdsa';
        exit;
        // Process action
    }
}
