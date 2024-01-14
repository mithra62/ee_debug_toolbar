<?php

namespace DebugToolbar\Errors\Extensions;

use DebugToolbar\Panels\Model;

class EeDebugToolbarAddPanel extends AbstractHook
{
    public function process(array $panels = [], array $vars = []): array
    {

        ee()->benchmark->mark('eedt_errors_start');
        $panels = (ee()->extensions->last_call != '' ? ee()->extensions->last_call : $panels);

        $vars['panel_fetch_url'] = $this->toolbar->createActUrl('get_panel_logs', 'Eedt_logs_ext');
        $vars['theme_img_url'] = ee('ee_debug_toolbar:OutputService')->themeUrl() . 'eedt_errors/images/';
        $vars['theme_js_url'] = ee('ee_debug_toolbar:OutputService')->themeUrl() . 'eedt_errors/js/';
        $vars['theme_css_url'] = ee('ee_debug_toolbar:OutputService')->themeUrl() . 'eedt_errors/css/';

        $icon_img = $vars['theme_img_url'] . 'good.png';


        $panels['eedt_errors'] = new Model();
        $panels['eedt_errors']->setName('log_viewer');
        $panels['eedt_errors']->setButtonIcon($icon_img);
        $panels['eedt_errors']->setButtonLabel(lang('Errors'));
        $panels['eedt_errors']->setPanelContents(ee()->load->view('eedt_errors', $vars, true));
        $panels['eedt_errors']->setPanelFetchUrl($vars['panel_fetch_url']);

        ee()->benchmark->mark('eedt_errors_end');

        return $panels;
    }
}
