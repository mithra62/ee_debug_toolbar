<?php

namespace DebugToolbar\Errors\Extensions;

class SessionsEnd extends AbstractHook
{
    public function process($session)
    {
        //check for eedt installed
        if (!ee()->db->table_exists('eedt_settings')) {
            return $session;
        }

        $settings = ee('eedt:SettingsService')->getSettings();
        if (!empty($settings['error_handler']) && $settings['error_handler'] == 'toolbar') {
            $error_handler = ee('eedt_errors:ErrorHandlerService');
            $error_handler->register();
        }

        return $session;
    }
}
