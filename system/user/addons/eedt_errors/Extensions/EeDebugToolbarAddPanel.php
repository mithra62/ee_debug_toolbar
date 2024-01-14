<?php

namespace DebugToolbar\Errors\Extensions;

use DebugToolbar\Panels\Model;

class EeDebugToolbarAddPanel extends AbstractHook
{
    public function process(array $panels = [], array $vars = []): array
    {
        ee()->benchmark->mark('eedt_errors_start');
        $settings = $this->toolbar->getSettings();
        if($settings['error_handler'] !== 'toolbar') {
            return $panels;
        }

        $panels = (ee()->extensions->last_call != '' ? ee()->extensions->last_call : $panels);

        $vars['panel_fetch_url'] = $this->toolbar->getActionUrl('getPanelErrors', 'Eedt_errors');
        $vars['theme_img_url'] = ee('ee_debug_toolbar:OutputService')->themeUrl() . 'eedt_errors/images/';
        $vars['theme_js_url'] = ee('ee_debug_toolbar:OutputService')->themeUrl() . 'eedt_errors/js/';
        $vars['theme_css_url'] = ee('ee_debug_toolbar:OutputService')->themeUrl() . 'eedt_errors/css/';

        $icon_img = $vars['theme_img_url'] . 'good.png';
        if(file_exists(ee('eedt_errors:LoggerService')->getLogFilePath()) && filesize(ee('eedt_errors:LoggerService')->getLogFilePath()) > 0) {
            $icon_img = $vars['theme_img_url'] . 'error.png';
        }

        $panels['eedt_errors'] = new Model();
        $panels['eedt_errors']->setName('eedt_errors');
        $panels['eedt_errors']->setButtonIcon($icon_img);
        $panels['eedt_errors']->setButtonLabel(lang('Errors'));
        $panels['eedt_errors']->setPanelContents('');
        $panels['eedt_errors']->setPanelFetchUrl($vars['panel_fetch_url']);
        $panels['eedt_errors']->addJs($vars['theme_js_url'] . 'eedt_errors.js');

        ee()->benchmark->mark('eedt_errors_end');

        return $panels;
    }
}
