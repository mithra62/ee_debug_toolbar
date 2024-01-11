<?php

use ExpressionEngine\Service\Addon\Extension;

class Eedt_logs_ext extends Extension
{
    /**
     * @var string
     */
    protected $addon_name = 'eedt_logs';

    public $eedt_act = ['get_panel_logs'];

    public function __construct($settings = '')
    {
        ee()->lang->loadfile('eedt_logs');
        ee()->load->add_package_path(PATH_THIRD . 'ee_debug_toolbar/');
        ee()->load->add_package_path(PATH_THIRD . 'eedt_logs/');
    }

    public function get_panel_logs()
    {
        $log_path = SYSPATH . "user/logs/";
        $vars['logs_enabled'] = false;
        if (ee()->config->config['log_threshold'] >= 1) {
            $vars['logs_enabled'] = true;
        }

        $vars['log_dir_writable'] = false;
        if (is_writable($log_path)) {
            $vars['log_dir_writable'] = true;
        }

        if (!is_readable($log_path)) {
            echo lang('log_dir_not_readable');
            exit;
        } else {
            $d = dir($log_path);
            $log_files = [];
            while (false !== ($entry = $d->read())) {
                if ($entry == '.' || $entry == '..') {
                    continue;
                }

                $log_files[$entry] = $entry;
            }
            $d->close();
            if (count($log_files) == '0') {
                echo lang('no_log_files');
                exit;
            }
        }

        $vars['latest_log'] = $log_path . end($log_files);
        $vars['log_files'] = $log_files;
        echo ee()->load->view('log_viewer', $vars, true);
        exit;
    }

    public function activate_extension()
    {
        $data = [];
        $data[] = [
            'class' => __CLASS__,
            'method' => 'ee_debug_toolbar_add_panel',
            'hook' => 'ee_debug_toolbar_add_panel',
            'settings' => '',
            'priority' => 10,
            'version' => DEBUG_TOOLBAR_LOGS_VERSION,
            'enabled' => 'y',
        ];

        foreach ($data as $ext) {
            ee()->db->insert('extensions', $ext);
        }

        return true;
    }

    public function update_extension($current = '')
    {
        if ($current == '' or $current == DEBUG_TOOLBAR_LOGS_VERSION) {
            return false;
        }

        ee()->db->where('class', __CLASS__);
        ee()->db->update(
            'extensions',
            ['version' => DEBUG_TOOLBAR_LOGS_VERSION]
        );
    }

    public function disable_extension()
    {
        ee()->db->where('class', __CLASS__);
        ee()->db->delete('extensions');
    }

}