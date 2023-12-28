<?php

namespace Mithra62\DebugToolbar\MemoryHistory\Extensions;

use ExpressionEngine\Library\CP\Form;

class EeDebugToolbarSettingsForm extends AbstractHook
{
    /**
     * @param Form $form
     * @return Form
     */
    public function process(Form $form): Form
    {
        $settings = $this->toolbar->getSettings();
        $options = [
            'bottom left' => 'bottom-left',
            'top left' => 'top-left',
            'top right' => 'top-right',
            'bottom right' => 'bottom-right'
        ];

        $field_group = $form->getGroup('eedt_memory_history.form.header.settings');
        $field_set = $field_group->getFieldSet('eedt_memory_history.form.position');
        $field_set->setDesc('eedt_memory_history.form.desc.form.position');
        $field = $field_set->getField('memory_history_position', 'select');
        $field->setChoices($options)
            ->setValue($settings['memory_history_position']);

        return $form;
    }
}
