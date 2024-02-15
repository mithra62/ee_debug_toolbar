<?php

namespace DebugToolbar\Extensions;

use ExpressionEngine\Service\Addon\Controllers\Extension\AbstractRoute;

class TemplatePostParse extends AbstractRoute
{
    public function process($final_template, $is_partial, $site_id)
    {


//        print_r(ee()->TMPL->tag_data);
//        print_r(ee()->TMPL->templates_loaded);
        //print_r(ee()->TMPL);
        //exit;
        //echo "\n";
        return $final_template;
        //print_r(ee()->TMPL->tagproper);
        //print_r(ee()->TMPL->search_fields);
        //print_r(ee()->TMPL->templates_loaded);
        exit;
        echo 'fdsa';
        exit;
    }
}
