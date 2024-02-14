<?php

namespace DebugToolbar\Errors\Actions;

use DebugToolbar\Actions\AbstractAction;

class ClearErrorLog extends AbstractAction
{
    public function processDebug()
    {
        if (!ee('eedt:ToolbarService')->canViewToolbar()) {
            return;
        }

        ee('eedt_errors:LoggerService')->deleteLog();
        exit;
    }
}
