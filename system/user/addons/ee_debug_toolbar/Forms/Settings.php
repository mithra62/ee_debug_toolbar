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

        $field_set = $field_group->getFieldSet('eedt_errors.form.allowed_roles');
        $field_set->set('group', 'error_handler')->setDesc('eedt_errors.form.desc.allowed_roles');
        $field = $field_set->getField('allowed_roles', 'checkbox');
        $field->setValue($this->get('allowed_roles'))
            ->setChoices($this->roleOptions());

        $field_group = $form->getGroup('eedt_perf_alerts.form.header.settings');
        $field_set = $field_group->getFieldSet('eedt_perf_alerts.form.max_exec_time');
        $field_set->setDesc('eedt_perf_alerts.form.desc.form.max_exec_time');
        $field = $field_set->getField('max_exec_time', 'number');
        $field->setValue($this->get('max_exec_time'));

        $field_set = $field_group->getFieldSet('eedt_perf_alerts.form.max_memory');
        $field_set->setDesc('eedt_perf_alerts.form.desc.form.max_memory');
        $field = $field_set->getField('max_memory', 'number');
        $field->setValue($this->get('max_memory'));

        $field_set = $field_group->getFieldSet('eedt_perf_alerts.form.max_queries');
        $field_set->setDesc('eedt_perf_alerts.form.desc.form.max_queries');
        $field = $field_set->getField('max_queries', 'number');
        $field->setValue($this->get('max_queries'));

        $field_set = $field_group->getFieldSet('eedt_perf_alerts.form.max_sql_time');
        $field_set->setDesc('eedt_perf_alerts.form.desc.form.max_sql_time');
        $field = $field_set->getField('max_sql_time', 'number');
        $field->setValue($this->get('max_sql_time'));

        $field_set = $field_group->getFieldSet('eedt_perf_alerts.form.max_query_time');
        $field_set->setDesc('eedt_perf_alerts.form.desc.form.max_query_time');
        $field = $field_set->getField('max_query_time', 'number');
        $field->setValue($this->get('max_query_time'));

        $field_set = $field_group->getFieldSet('eedt_perf_alerts.form.max_query_memory');
        $field_set->setDesc('eedt_perf_alerts.form.desc.form.max_query_memory');
        $field = $field_set->getField('max_query_memory', 'number');
        $field->setValue($this->get('max_query_memory'));

        if (ee()->extensions->active_hook('ee_debug_toolbar_settings_form') === TRUE) {
            $form = ee()->extensions->call('ee_debug_toolbar_settings_form', $form);
        }

        $form->asTab();

        return $form->toArray();
    }

    /**
     * @return array
     */
    protected function roleOptions(): array
    {
        $groups = [];
        $query = ee('Model')
            ->get('Role')
            ->order('name', 'asc')
            ->all();

        foreach ($query as $row) {
            $groups[$row->role_id] = $row->name;
        }

        return $groups;
    }
}