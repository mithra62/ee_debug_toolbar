<?php

namespace DebugToolbar\Logs\Extensions;

use DebugToolbar\Panels\Model;

class EeDebugToolbarAddPanel extends AbstractHook
{
    public function process(array $panels = [], array $vars = []): array
    {
        ee()->benchmark->mark('eedt_log_viewer_start');
        $panels = (ee()->extensions->last_call != '' ? ee()->extensions->last_call : $panels);

        $vars['panel_fetch_url'] = $this->toolbar->createActUrl('get_panel_logs', 'Eedt_logs_ext');
        $vars['theme_img_url'] = eedt_theme_url() . 'eedt_logs/images/';
        $vars['theme_js_url'] = eedt_theme_url() . 'eedt_logs/js/';
        $vars['theme_css_url'] = eedt_theme_url() . 'eedt_logs/css/';

        $panels['log_viewer'] = new Model();
        $panels['log_viewer']->setName('log_viewer');
        $panels['log_viewer']->setButtonIcon($vars['theme_img_url'] . 'logs.png');
        $panels['log_viewer']->setButtonLabel(lang('Logs'));
        $panels['log_viewer']->setPanelContents(ee()->load->view('log_viewer', $vars, true));
        $panels['log_viewer']->setPanelFetchUrl($vars['panel_fetch_url']);

        ee()->benchmark->mark('eedt_log_viewer_end');

        return $panels;
    }
}
