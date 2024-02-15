<?php

namespace DebugToolbar\Email\Extensions;

use ExpressionEngine\Library\CP\Form;

class EedtSettingsForm extends AbstractHook
{
    public function process(Form $form): Form
    {
        $settings = $this->toolbar->getSettings();

        $field_group = $form->getGroup('eedt_email.form.header.settings');
        $field_set = $field_group->getFieldSet('eedt_email.form.email_action');
        $field_set->setDesc('eedt_email.form.desc.email_action');
        $field = $field_set->getField('email_action', 'select');
        $field->setValue($settings['email_action'])
            ->set('group_toggle', ['log' => 'logging', 'log_send' => 'logging'])
            ->setChoices(ee('eedt_email:EmailService')->getActionOptions());

        $field_set = $field_group->getFieldSet('eedt_email.form.email_log_dir');
        $field_set->set('group', 'logging')->setDesc('eedt_email.form.desc.form.email_log_dir');
        $field = $field_set->getField('email_log_dir', 'text');
        $field->setValue($settings['email_log_dir']);

        return $form;
    }
}
