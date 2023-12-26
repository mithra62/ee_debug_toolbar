<?php

use Mithra62\DebugToolbar\Services\ToolbarService;

define('DEBUG_TOOLBAR_ADDON_NAME', 'Custom Addon');
define('DEBUG_TOOLBAR_VERSION', '2.0');

return [
    'author' => 'Eric Lamb',
    'author_url' => 'https://mithra62.com',
    'docs_url' => 'https://www.mithra62.com/',
    'name' => DEBUG_TOOLBAR_ADDON_NAME,
    'description' => 'Adds an unobtrusive interface for debugging output',
    'version' => DEBUG_TOOLBAR_VERSION,
    'namespace' => 'Mithra62\DebugToolbar',
    'settings_exist' => true,
    'tests' => [
        'path' => 'src/tests'
    ],
    'services.singletons' => [
        'ToolbarService' => function ($addon) {
            return new ToolbarService();
        },
    ],
];
