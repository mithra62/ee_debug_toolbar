<?php

namespace DebugToolbar\Actions;

use DebugToolbar\Exceptions\InvalidActionCallException;
use DebugToolbar\Services\ToolbarService;
use ExpressionEngine\Service\Addon\Controllers\Action\AbstractRoute;

abstract class AbstractAction extends AbstractRoute
{
    /**
     * @var string
     */
    protected string $cache_dir = '';

    /**
     * @var array|string[]
     */
    protected array $panel_order = [
        'Copyright',
        'Variables',
        'Files',
        'Memory',
        'Time',
        'Config',
        'Database',
    ];

    /**
     * @var ToolbarService|mixed
     */
    protected ToolbarService $toolbar;

    public function __construct()
    {
        ee()->lang->loadfile('ee_debug_toolbar');
        ee()->load->add_package_path(PATH_THIRD . 'ee_debug_toolbar/');
        $this->toolbar = ee('ee_debug_toolbar:ToolbarService');

        //run the garbage collection against the cache
        ee('ee_debug_toolbar:GarbageCollectionService')->run();
        $this->cache_dir = SYSDIR . '/user/cache/eedt/';
    }

    /**
     * @return void
     */
    public function process()
    {
        throw new InvalidActionCallException("Invalid Debug Action Request! This can only be called through the Debug Toolbar!");
    }

    /**
     * @return mixed
     */
    abstract function processDebug();
}