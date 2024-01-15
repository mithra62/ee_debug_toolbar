<?php

namespace DebugToolbar\Logs\Actions;

use ExpressionEngine\Service\Addon\Controllers\Action\AbstractRoute;

class GetPanelLogs extends AbstractRoute
{
    public function process()
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
}
