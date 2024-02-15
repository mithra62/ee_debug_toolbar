<?php

if (!defined('DEBUG_TOOLBAR_EMAIL_ADDON_NAME')) {
    define('DEBUG_TOOLBAR_EMAIL_ADDON_NAME', 'Debug Toolbar - Email');
}

if (!defined('DEBUG_TOOLBAR_EMAIL_VERSION')) {
    define('DEBUG_TOOLBAR_EMAIL_VERSION', '2.0.0');
}

use DebugToolbar\Email\Services\EmailService;

return [
    'author' => 'mithra62',
    'author_url' => 'https://github.com/mithra62/ee_debug_toolbar',
    'docs_url' => 'https://github.com/mithra62/ee_debug_toolbar/wiki',
    'name' => DEBUG_TOOLBAR_EMAIL_ADDON_NAME,
    'description' => 'Allows control over email during development',
    'version' => DEBUG_TOOLBAR_EMAIL_VERSION,
    'namespace' => 'DebugToolbar\Email',
    'settings_exist' => false,
    'services' => [
        'EmailService' => function ($addon) {
            return new EmailService();
        },
    ]
];