<?php

use ExpressionEngine\Service\Addon\Extension;

class Eedt_perf_alerts_ext extends Extension
{
    /**
     * @var string
     */
    protected $addon_name = 'eedt_perf_alerts';

    public function __construct($settings = '')
    {
        ee()->lang->loadfile('eedt_perf_alerts');
        ee()->load->add_package_path(PATH_THIRD . 'ee_debug_toolbar/');
        ee()->load->add_package_path(PATH_THIRD . 'eedt_perf_alerts/');
    }

    public function activate_extension()
    {
        $data = [];
        $data[] = [
            'class' => __CLASS__,
            'method' => 'ee_debug_toolbar_mod_panel',
            'hook' => 'ee_debug_toolbar_mod_panel',
            'settings' => '',
            'priority' => 1,
            'version' => DEBUG_TOOLBAR_PERF_ALERTS_VERSION,
            'enabled' => 'y'
        ];

        $data[] = [
            'class' => __CLASS__,
            'method' => 'ee_debug_toolbar_settings_form',
            'hook' => 'ee_debug_toolbar_settings_form',
            'settings' => '',
            'priority' => 1,
            'version' => DEBUG_TOOLBAR_PERF_ALERTS_VERSION,
            'enabled' => 'y'
        ];

        $data[] = [
            'class' => __CLASS__,
            'method' => 'ee_debug_toolbar_init_settings',
            'hook' => 'ee_debug_toolbar_init_settings',
            'settings' => '',
            'priority' => 5,
            'version' => DEBUG_TOOLBAR_PERF_ALERTS_VERSION,
            'enabled' => 'y'
        ];

        foreach ($data as $ext) {
            ee()->db->insert('extensions', $ext);
        }

        return true;
    }

    public function update_extension($current = '')
    {
        if ($current == '' or $current == DEBUG_TOOLBAR_PERF_ALERTS_VERSION) {
            return false;
        }

        ee()->db->where('class', __CLASS__);
        ee()->db->update(
            'extensions',
            array('version' => DEBUG_TOOLBAR_PERF_ALERTS_VERSION)
        );
    }

    public function disable_extension()
    {
        ee()->db->where('class', __CLASS__);
        ee()->db->delete('extensions');
    }

}