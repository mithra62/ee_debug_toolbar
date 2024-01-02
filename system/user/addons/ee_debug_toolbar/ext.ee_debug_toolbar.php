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

    protected array $panel_order = [
        'Copyright',
        'Variables',
        'Files',
        'Memory',
        'Time',
        'Config',
        'Database'
    ];

    /**
     * Fetches cached panel HTML output
     */
    public function get_panel_data()
    {
        $panel = ee()->input->get('panel', false);
        if (!$panel) {
            return;
        }

        $this->cache_dir =  SYSDIR. '/user/cache/eedt/';
        $this->toolbar = ee('ee_debug_toolbar:ToolbarService');
        //the cache file is just an XML so we check for existance, node, and display. easy
        $file = $this->cache_dir . $this->toolbar->makeCacheFilename() . '.gz';

        if (file_exists($file) && is_readable($file)) {
            $gz = gzfile($file);
            $gz = implode("", $gz);
            $xml = simplexml_load_string($gz);
            $panel_node = $panel . '_panel';
            if (isset($xml->panels->$panel_node->output) && $xml->panels->$panel_node->output != '') {
                echo base64_decode($xml->panels->$panel_node->output);
            }
            exit;
        }
    }

    /**
     * Allows JS to communicate directly with a panel extension
     */
    public function panel_ajax()
    {
        $data = [];
        $panel = ee()->input->get("panel", false);
        $method = ee()->input->get("method", false);

        if (!$panel || $method) {
            return;
        }

        if (in_array($panel, $this->panel_order)) {
            //Native Panel
            ee()->load->file(PATH_THIRD . 'ee_debug_toolbar/panels/Eedt_' . $panel . '_panel.php');
            $class = 'Eedt_' . $panel . '_panel';

            if (class_exists($class)) {

                $instance = new $class();

                if (method_exists($instance, $method)) {
                    $data = $instance->$method();
                }
            }


        } else {
            //Third Party panel

            /**
             * TODO
             * I realise now that we need to somehow specify the path to the class since
             * the panel name will not necessarily match up with the extension name, so we cant
             * make that assumption.
             */
        }

        if ($data) {
            ee()->output->send_ajax_response($data);
        }
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
