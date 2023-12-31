<?php

namespace DebugToolbar\Extensions;

class SessionsEnd extends AbstractHook
{
    public function process($session)
    {
        $settings = ee('ee_debug_toolbar:SettingsService')->getSettings();
        if (!empty($settings['error_handler']) && $settings['error_handler'] == 'toolbar') {
            $error_handler = ee('ee_debug_toolbar:ErrorHandlerService');
            echo 'fdsa';
            exit;
            $error_handler->register();
        }

        return $session;
    }
}
