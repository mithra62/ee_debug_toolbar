<?php


if (!defined('DEBUG_TOOLBAR_MEMORY_HISTORY_ADDON_NAME')) {
    define('DEBUG_TOOLBAR_MEMORY_HISTORY_ADDON_NAME', 'Debug Toolbar - Memory History');
}

if (!defined('DEBUG_TOOLBAR_MEMORY_HISTORY_VERSION')) {
    define('DEBUG_TOOLBAR_MEMORY_HISTORY_VERSION', '2.0.0');
}

return [
    'name' => DEBUG_TOOLBAR_MEMORY_HISTORY_ADDON_NAME,
    'description' => 'Tracks memory usage across multiple pages.',
    'version' => DEBUG_TOOLBAR_MEMORY_HISTORY_VERSION,
    'author' => 'me',
    'author_url' => 'me',
    'namespace' => 'Mithra62\DebugToolbar\MemoryHistory',
    'settings_exist' => false
];
