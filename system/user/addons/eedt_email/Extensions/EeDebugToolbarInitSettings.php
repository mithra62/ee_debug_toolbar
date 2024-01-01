<?php

namespace DebugToolbar\Email\Extensions;

class EeDebugToolbarInitSettings extends AbstractHook
{
    /**
     * The extensions settings if none exist
     * @var array
     */
    public array $default_settings = [
        'max_exec_time' => 'send',
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