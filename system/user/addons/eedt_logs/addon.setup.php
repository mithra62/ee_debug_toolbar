<?php

if (!defined('DEBUG_TOOLBAR_LOGS_ADDON_NAME')) {
    define('DEBUG_TOOLBAR_LOGS_ADDON_NAME', 'Debug Toolbar - Logs');
}

if (!defined('DEBUG_TOOLBAR_LOGS_VERSION')) {
    define('DEBUG_TOOLBAR_LOGS_VERSION', '2.0.0');
}

return [
    'author' => 'mithra62',
    'author_url' => 'https://github.com/mithra62/ee_debug_toolbar',
    'docs_url' => 'https://github.com/mithra62/ee_debug_toolbar/wiki',
    'name' => DEBUG_TOOLBAR_LOGS_ADDON_NAME,
    'description' => 'Displays the Developer Log in the toolbar.',
    'version' => DEBUG_TOOLBAR_LOGS_VERSION,
    'namespace' => 'DebugToolbar\Logs',
    'settings_exist' => false,
    'services' => [

    ]
];
