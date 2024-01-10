<?php

namespace DebugToolbar\Extensions;

use ExpressionEngine\Service\Addon\Controllers\Extension\AbstractRoute;

class TemplatePostParse extends AbstractRoute
{
    public function process($final_template, $is_partial, $site_id, $currentTemplateInfo)
    {
        ee('ee_debug_toolbar:TrackerService')->trackTemplate($currentTemplateInfo);
        return $final_template;
    }
}
