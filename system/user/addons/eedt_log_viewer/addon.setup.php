<?php

return [
    'name'              => 'EE Debug Toolbar - Log Viewer',
    'description'       => 'Displays the Developer Log in the toolbar.',
    'version'           => '1.1.0',
    'author'            => 'me',
    'author_url'        => 'me',
    'namespace'         => 'Me\TestAddon',
    'settings_exist'    => true,
    'fieldtypes'        => [
        'TestFieldType' => [
            'name' => 'TestFieldType',
            'compatibility' => 'text',
        ],
    ],
];
