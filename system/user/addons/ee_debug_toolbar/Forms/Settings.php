<?php
namespace Mithra62\DebugToolbar\Forms;

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

        return $form->toArray();
    }
}