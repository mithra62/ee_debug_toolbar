<?php

namespace DebugToolbar\Errors\Extensions;

use ExpressionEngine\Library\CP\Form;

class EeDebugToolbarSettingsForm extends AbstractHook
{
    public function process(Form $form): Form
    {
        $settings = $this->toolbar->getSettings();

        $field_group = $form->getGroup('eedt_errors.form.header.settings');
        $field_set = $field_group->getFieldSet('eedt.form.error_handler');
        $field_set->setDesc('eedt.form.desc.error_handler');
        $field = $field_set->getField('error_handler', 'select');
        $field->setValue($settings['error_handler'])
            ->set('group_toggle', ['toolbar' => 'error_handler'])
            ->setChoices(['ee' => 'ExpressionEngine', 'toolbar' => 'Debug Toolbar']);

        $field_set = $field_group->getFieldSet('eedt.form.hide_error_codes');
        $field_set->set('group', 'error_handler')->setDesc('eedt.form.desc.hide_error_codes');
        $field = $field_set->getField('hide_error_codes', 'checkbox');
        $field->setValue($settings['hide_error_codes'])
            ->setChoices(ee('ee_debug_toolbar:ToolbarService')->getDisplayErrorCodes());

        $field_set = $field_group->getFieldSet('eedt.form.error_log_path');
        $field_set->set('group', 'error_handler')->setDesc('eedt.form.desc.error_log_path');
        $field = $field_set->getField('error_log_path', 'text');
        $field->setValue($settings['error_log_path']);

        return $form;
    }
}