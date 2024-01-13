<?php

namespace DebugToolbar\Extensions;

use ExpressionEngine\Service\Addon\Controllers\Extension\AbstractRoute;

class TemplateFetchTemplate extends AbstractRoute
{
    public function process($row)
    {
        ee('ee_debug_toolbar:TrackerService')->trackTemplate($row);
    }
}
