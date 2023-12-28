<?php

namespace Mithra62\DebugToolbar\ControlPanel\Routes;

use ExpressionEngine\Service\Addon\Controllers\Mcp\AbstractRoute;
use Mithra62\DebugToolbar\Forms\Settings as SettingsForm;

class Settings extends AbstractRoute
{
    protected $addon_name = 'ee_debug_toolbar';

    /**
     * @param false $id
     * @return AbstractRoute
     */
    public function process($id = false)
    {
        $this->settings = ee('ee_debug_toolbar:ToolbarService')->getSettings();
        $vars['cp_page_title'] = lang('eedt.settings');
        $vars['base_url'] = $this->url('settings');
        $vars['save_btn_text'] = lang('eedt.save');
        $vars['save_btn_text_working'] = lang('eedt.saving');

        $form = new SettingsForm();
        $form->setData($this->settings);
        if (ee()->input->server('REQUEST_METHOD') === 'POST') {
            $form->setData($_POST);
            $result = $form->validate();
            if ($result->isValid()) {
                ee('ee_debug_toolbar:SettingsService')->updateSettings($_POST);
                ee('CP/Alert')->makeInline('shared-form')
                    ->asSuccess()
                    ->withTitle(lang('eedt.settings_saved'))
                    ->defer();

                ee()->functions->redirect($this->url('settings'));
                exit;
            } else {
                $vars['errors'] = $result;
                ee('CP/Alert')->makeInline('shared-form')
                    ->asIssue()
                    ->withTitle(lang('eedt.error.settings_save'))
                    ->now();
            }
        }

        $vars += $form->generate();

        $this->addBreadcrumb($this->url('edit'), 'eedt.settings');
        $this->setBody('settings', $vars);
        $this->setHeading('eedt.header.settings_save');
        return $this;
    }
}
