<?php
namespace DebugToolbar\Actions;

use DebugToolbar\Toolbar\GarbageCollection;
use ExpressionEngine\Service\Addon\Controllers\Action\AbstractRoute;

abstract class AbstractAction extends AbstractRoute
{

    public function __construct()
    {
        ee()->lang->loadfile('ee_debug_toolbar');
        ee()->load->add_package_path(PATH_THIRD . 'ee_debug_toolbar/');

        //run the garbage collection against the cache
        $gc = new GarbageCollection;
        $gc->run();
    }
}