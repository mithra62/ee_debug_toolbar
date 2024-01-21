<?php

namespace DebugToolbar\Errors\Extensions;

use ExpressionEngine\Service\Addon\Controllers\Extension\AbstractRoute;
use DebugToolbar\Services\ToolbarService;

abstract class AbstractHook extends AbstractRoute
{
    /**
     * @var ToolbarService|mixed
     */
    protected ToolbarService $toolbar;

    public function __construct($settings = '')
    {
        ee()->lang->load('eedt_errors', $idiom = '', $return = false, $add_suffix = true, $alt_path = __DIR__ . '/../');
        $this->toolbar = ee('ee_debug_toolbar:ToolbarService');
    }
}