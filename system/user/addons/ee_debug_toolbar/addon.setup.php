<?php

use DebugToolbar\Services\ToolbarService;
use DebugToolbar\Services\SettingsService;
use DebugToolbar\Services\ErrorHandlerService;
use DebugToolbar\Services\LoggerService;

if(!defined('DEBUG_TOOLBAR_ADDON_NAME')) {
    define('DEBUG_TOOLBAR_ADDON_NAME', 'Debug Toolbar');
}

if(!defined('DEBUG_TOOLBAR_VERSION')) {
    define('DEBUG_TOOLBAR_VERSION', '2.0.0');
}

return [
    'author' => 'mithra62',
    'author_url' => 'https://github.com/mithra62/ee_debug_toolbar',
    'docs_url' => 'https://github.com/mithra62/ee_debug_toolbar/wiki',
    'name' => DEBUG_TOOLBAR_ADDON_NAME,
    'description' => 'Adds an unobtrusive interface for debugging output',
    'version' => DEBUG_TOOLBAR_VERSION,
    'namespace' => 'DebugToolbar',
    'settings_exist' => true,
    'services.singletons' => [
        'ToolbarService' => function ($addon) {
            return new ToolbarService();
        },
        'SettingsService' => function ($addon) {
            return new SettingsService();
        },
        'ErrorHandlerService' => function ($addon) {
            return new ErrorHandlerService();
        },
        'LoggerService' => function ($addon) {
            return new LoggerService();
        },
    ],
];
