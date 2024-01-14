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
        'log_error_codes' => [
            E_ERROR,
            E_WARNING,
            E_PARSE,
            E_NOTICE,
            E_CORE_ERROR,
            E_CORE_WARNING,
            E_COMPILE_ERROR,
            E_COMPILE_WARNING,
            E_USER_ERROR,
            E_USER_WARNING,
            E_USER_NOTICE,
            E_STRICT,
            E_RECOVERABLE_ERROR,
            E_DEPRECATED,
            E_USER_DEPRECATED,
        ]
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