<?php

use ExpressionEngine\Service\Addon\Extension;
use DebugToolbar\Services\ToolbarService;
class Ee_debug_toolbar_ext extends Extension
{
    protected $addon_name = 'ee_debug_toolbar';

    /**
     * The full path to store the cached debug output
     * @var string
     */
    protected string $cache_dir = '';

    /**
     * List of methods available for use with EEDT ACT
     * @var array
     */
    public array $eedt_act = [
        'get_panel_data',
        'panel_ajax'
    ];

    protected ToolbarService $toolbar;


    /**
     * Fetches cached panel HTML output
     */
    public function get_panel_data()
    {

    }

    /**
     * Allows JS to communicate directly with a panel extension
     */
    public function panel_ajax()
    {

    }

    public function settings()
    {
        ee()->functions->redirect(BASE . AMP . 'C=addons_modules&M=show_module_cp&module=ee_debug_toolbar&method=settings');
    }

    public function activate_extension()
    {
        return true;
    }

    public function update_extension($current = '')
    {
        return true;
    }

    public function disable_extension()
    {
        return true;
    }

}
