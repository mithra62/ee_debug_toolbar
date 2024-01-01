<?php

namespace DebugToolbar\Email\Extensions;

use ExpressionEngine\Library\CP\Form;

class EeDebugToolbarSettingsForm extends AbstractHook
{
    public function process(Form $form): Form
    {
        $settings = $this->toolbar->getSettings();

        $field_group = $form->getGroup('eedt_email.form.header.settings');
        $field_set = $field_group->getFieldSet('eedt_email.form.max_exec_time');
        $field_set->setDesc('eedt_email.form.desc.form.max_exec_time');
        $field = $field_set->getField('max_exec_time', 'number');
        $field->setValue($settings['max_exec_time']);

        return $form;
    }
}
