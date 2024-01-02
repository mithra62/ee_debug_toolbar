<?php

if (!defined('DEBUG_TOOLBAR_ERRORS_ADDON_NAME')) {
    define('DEBUG_TOOLBAR_ERRORS_ADDON_NAME', 'Debug Toolbar - Errors');
}

if (!defined('DEBUG_TOOLBAR_ERRORS_VERSION')) {
    define('DEBUG_TOOLBAR_ERRORS_VERSION', '2.0.0');
}

return [
    'author' => 'mithra62',
    'author_url' => 'https://github.com/mithra62/ee_debug_toolbar',
    'docs_url' => 'https://github.com/mithra62/ee_debug_toolbar/wiki',
    'name' => DEBUG_TOOLBAR_ERRORS_ADDON_NAME,
    'description' => 'Tools for Errors and Error Handling',
    'version' => DEBUG_TOOLBAR_ERRORS_VERSION,
    'namespace' => 'DebugToolbar\Errors',
    'settings_exist' => false,
    'services' => [

    ]
];
