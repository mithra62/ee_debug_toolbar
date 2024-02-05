<?php

namespace DebugToolbar\Extensions;

use ExpressionEngine\Service\Addon\Controllers\Extension\AbstractRoute;
use DebugToolbar\Services\ToolbarService;

abstract class AbstractHook extends AbstractRoute
{
    /**
     * The extensions default settings
     *
     * @var array
     */
    public $settings = [
        'theme' => 'default'
    ];

    /**
     * Persistent storage to hold settings across the
     * multiple class initialisations by EE and then CI
     *
     * @var array
     */
    static $persistent_settings = [];

    /**
     * The extension name
     *
     * @var string
     */
    public $name = '';

    /**
     * The extension version
     *
     * @var float
     */
    public $version = '';

    /**
     * Used nowhere and not really needed (ya hear me ElisLab?!?!)
     *
     * @var string
     */
    public $description = '';

    /**
     * We're doing our own settings now so set this to off.
     *
     * @var string
     */
    public $settings_exist = 'y';

    /**
     * Where to get help
     *
     * @var string
     */
    public $docs_url = 'https://github.com/mithra62/ee_debug_toolbar/wiki';

    /**
     * The full path to store the cached debug output
     * @var string
     */
    protected string $cache_dir = '';

    /**
     * The order the default panels appear in.
     * Also used to differentiate the native panels from third party panels
     * @var array
     */
    protected array $panel_order = [
        'Copyright',
        'Variables',
        'Files',
        'Memory',
        'Time',
        'Config',
        'Database'
    ];

    protected ToolbarService $toolbar;

    public function __construct($settings = '')
    {
        ee()->lang->loadfile('ee_debug_toolbar');
        $path = dirname(realpath(__FILE__));
        $this->name = lang('ee_debug_toolbar_module_name');
        $this->description = lang('ee_debug_toolbar_module_description');
        ee()->load->add_package_path(PATH_THIRD . 'ee_debug_toolbar/');
        $this->toolbar = ee('ee_debug_toolbar:ToolbarService');
        $this->cache_dir =  $this->toolbar->getCachePath();
        if (!is_dir($this->cache_dir)) {
            mkdir($this->cache_dir, 0777, true);
        }
    }
}