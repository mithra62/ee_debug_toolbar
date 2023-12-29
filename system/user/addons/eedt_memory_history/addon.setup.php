<?php


if (!defined('DEBUG_TOOLBAR_MEMORY_HISTORY_ADDON_NAME')) {
    define('DEBUG_TOOLBAR_MEMORY_HISTORY_ADDON_NAME', 'Debug Toolbar - Memory History');
}

if (!defined('DEBUG_TOOLBAR_MEMORY_HISTORY_VERSION')) {
    define('DEBUG_TOOLBAR_MEMORY_HISTORY_VERSION', '2.0.0');
}

return [
    'author' => 'mithra62',
    'author_url' => 'https://github.com/mithra62/ee_debug_toolbar',
    'docs_url' => 'https://github.com/mithra62/ee_debug_toolbar/wiki',
    'name' => DEBUG_TOOLBAR_MEMORY_HISTORY_ADDON_NAME,
    'description' => 'Tracks memory usage across multiple pages.',
    'version' => DEBUG_TOOLBAR_MEMORY_HISTORY_VERSION,
    'namespace' => 'DebugToolbar\MemoryHistory',
    'settings_exist' => false
];
