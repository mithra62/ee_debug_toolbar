<?php
namespace DebugToolbar\Forms;

use ExpressionEngine\Library\CP\Form\AbstractForm;
use ExpressionEngine\Library\CP\Form;

class Settings extends AbstractForm
{
    public function generate(): array
    {
        $form = new Form;

        $field_group = $form->getGroup('eedt.form.header.settings');
        $field_set = $field_group->getFieldSet('eedt.form.theme');
        $field_set->setDesc('eedt.form.desc.theme');
        $field = $field_set->getField('theme', 'select');
        $field->setValue($this->get('theme', 'default'))
            ->setChoices(ee('ee_debug_toolbar:ToolbarService')->getThemes());

        $field_set = $field_group->getFieldSet('eedt.form.toolbar_position');
        $field_set->setDesc('eedt.form.desc.toolbar_position');
        $field = $field_set->getField('toolbar_position', 'select');
        $field->setValue($this->get('toolbar_position', 'bottom-left'))
            ->setChoices(ee('ee_debug_toolbar:ToolbarService')->toolbar_positions);

        $field_set = $field_group->getFieldSet('eedt.form.error_handler');
        $field_set->setDesc('eedt.form.desc.error_handler');
        $field = $field_set->getField('error_handler', 'select');
        $field->setValue($this->get('error_handler', 'default'))
            ->set('group_toggle', ['toolbar' => 'error_handler'])
            ->setChoices(['ee' => 'ExpressionEngine', 'toolbar' => 'Debug Toolbar']);

        $field_set = $field_group->getFieldSet('eedt.form.display_error_codes');
        $field_set
            ->set('group', 'error_handler')->setDesc('eedt.form.desc.display_error_codes');
        $field = $field_set->getField('display_error_codes', 'checkbox');
        $field->setValue($this->get('display_error_codes', []))
            ->setChoices(ee('ee_debug_toolbar:ToolbarService')->getDisplayErrorCodes());

        if (ee()->extensions->active_hook('ee_debug_toolbar_settings_form') === TRUE) {
            $form = ee()->extensions->call('ee_debug_toolbar_settings_form', $form);
        }

        return $form->toArray();
    }
}