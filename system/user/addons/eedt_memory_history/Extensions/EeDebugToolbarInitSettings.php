<?php

namespace Mithra62\DebugToolbar\MemoryHistory\Extensions;

use ExpressionEngine\Service\Addon\Controllers\Extension\AbstractRoute;

class EeDebugToolbarInitSettings extends AbstractRoute
{
    /**
     * @var array|string[]
     */
    protected array $default_settings = [
        'memory_history_position' => "top right",
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
