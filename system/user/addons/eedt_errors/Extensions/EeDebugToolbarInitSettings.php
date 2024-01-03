<?php

namespace DebugToolbar\Errors\Extensions;

class EeDebugToolbarInitSettings extends AbstractHook
{
    /**
     * The extensions settings if none exist
     * @var array
     */
    public array $default_settings = [
        'error_log_path' => '',
        'error_handler' => 'ee',
        'hide_error_codes' => [
            E_WARNING,
            E_NOTICE,
            E_USER_ERROR,
            E_DEPRECATED,
            E_STRICT
        ],
    ];

    /**
     * @param array $default_settings
     * @return array
     */
    public function process(array $default_settings): array
    {
        $default_settings = (ee()->extensions->last_call != '' ? ee()->extensions->last_call : $default_settings);
        return array_merge($default_settings, $this->default_settings);
    }
}