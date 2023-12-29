<?php

if (!defined('DEBUG_TOOLBAR_PERF_ALERTS_ADDON_NAME')) {
    define('DEBUG_TOOLBAR_PERF_ALERTS_ADDON_NAME', 'Debug Toolbar - Performance Alerts');
}

if (!defined('DEBUG_TOOLBAR_PERF_ALERTS_VERSION')) {
    define('DEBUG_TOOLBAR_PERF_ALERTS_VERSION', '2.0.0');
}

return [
    'author' => 'mithra62',
    'author_url' => 'https://github.com/mithra62/ee_debug_toolbar',
    'docs_url' => 'https://github.com/mithra62/ee_debug_toolbar/wiki',
    'name' => DEBUG_TOOLBAR_PERF_ALERTS_ADDON_NAME,
    'description' => 'Adds notifications to toolbar on suspicious performance',
    'version' => DEBUG_TOOLBAR_PERF_ALERTS_VERSION,
    'namespace' => 'DebugToolbar\PerfAlerts',
    'settings_exist' => false
];
