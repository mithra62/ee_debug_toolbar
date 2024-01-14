<?php

namespace DebugToolbar\Errors\Extensions;

use ExpressionEngine\Library\CP\Form;

class EeDebugToolbarSettingsForm extends AbstractHook
{
    public function process(Form $form): Form
    {
        $settings = $this->toolbar->getSettings();

        $field_group = $form->getGroup('eedt_errors.form.header.settings');
        $field_set = $field_group->getFieldSet('eedt_errors.form.error_handler');
        $field_set->setDesc('eedt_errors.form.desc.error_handler');
        $field = $field_set->getField('error_handler', 'select');
        $field->setValue($settings['error_handler'])
            ->set('group_toggle', ['toolbar' => 'error_handler'])
            ->setChoices(['ee' => 'ExpressionEngine', 'toolbar' => 'Debug Toolbar']);

        $field_set = $field_group->getFieldSet('eedt_errors.form.hide_error_codes');
        $field_set->set('group', 'error_handler')->setDesc('eedt_errors.form.desc.hide_error_codes');
        $field = $field_set->getField('hide_error_codes', 'checkbox');
        $field->setValue($settings['hide_error_codes'])
            ->setChoices($this->getDisplayErrorCodes());

        $field_set = $field_group->getFieldSet('eedt_errors.form.log_error_codes');
        $field_set->set('group', 'error_handler')->setDesc('eedt_errors.form.desc.log_error_codes');
        $field = $field_set->getField('log_error_codes', 'checkbox');
        $field->setValue($settings['log_error_codes'])
            ->setChoices($this->getDisplayErrorCodes());

        $field_set = $field_group->getFieldSet('eedt_errors.form.error_log_path');
        $field_set->set('group', 'error_handler')->setDesc('eedt_errors.form.desc.error_log_path');
        $field = $field_set->getField('error_log_path', 'text');
        $field->setValue($settings['error_log_path']);

        return $form;
    }



    /**
     * @return string[]
     */
    public function getDisplayErrorCodes(): array
    {
        return [
            E_ERROR => 'E_ERROR',
            E_WARNING => 'E_WARNING',
            E_PARSE => 'E_PARSE',
            E_NOTICE => 'E_NOTICE',
            E_CORE_ERROR => 'E_CORE_ERROR',
            E_CORE_WARNING => 'E_CORE_WARNING',
            E_COMPILE_ERROR => 'E_COMPILE_ERROR',
            E_COMPILE_WARNING => 'E_COMPILE_WARNING',
            E_USER_ERROR => 'E_USER_ERROR',
            E_USER_WARNING => 'E_USER_WARNING',
            E_USER_NOTICE => 'E_USER_NOTICE',
            E_STRICT => 'E_STRICT',
            E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
            E_DEPRECATED => 'E_DEPRECATED',
            E_USER_DEPRECATED => 'E_USER_DEPRECATED',
        ];
    }
}