<?php

namespace Mithra62\DebugToolbar\PerfAlerts\Extensions;

class EeDebugToolbarInitSettings extends AbstractHook
{
    /**
     * The extensions settings if none exist
     * @var array
     */
    public array $default_settings = [
        'max_exec_time' => 0.5,
        'max_memory' => 10,
        'max_queries' => 100,
        'max_sql_time' => 0.1,
        'max_query_time' => 0.01
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
