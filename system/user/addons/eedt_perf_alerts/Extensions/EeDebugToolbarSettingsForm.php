<?php

namespace Mithra62\DebugToolbar\PerfAlerts\Extensions;

use ExpressionEngine\Library\CP\Form;

class EeDebugToolbarSettingsForm extends AbstractHook
{
    public function process(Form $form): Form
    {
        $settings = $this->toolbar->getSettings();

        $field_group = $form->getGroup('eedt_perf_alerts.form.header.settings');
        $field_set = $field_group->getFieldSet('eedt_perf_alerts.form.max_exec_time');
        $field_set->setDesc('eedt_perf_alerts.form.desc.form.max_exec_time');
        $field = $field_set->getField('max_exec_time', 'number');
        $field->setValue($settings['max_exec_time']);

        $field_set = $field_group->getFieldSet('eedt_perf_alerts.form.max_memory');
        $field_set->setDesc('eedt_perf_alerts.form.desc.form.max_memory');
        $field = $field_set->getField('max_memory', 'number');
        $field->setValue($settings['max_memory']);

        $field_set = $field_group->getFieldSet('eedt_perf_alerts.form.max_queries');
        $field_set->setDesc('eedt_perf_alerts.form.desc.form.max_queries');
        $field = $field_set->getField('max_queries', 'number');
        $field->setValue($settings['max_queries']);

        $field_set = $field_group->getFieldSet('eedt_perf_alerts.form.max_sql_time');
        $field_set->setDesc('eedt_perf_alerts.form.desc.form.max_sql_time');
        $field = $field_set->getField('max_sql_time', 'number');
        $field->setValue($settings['max_sql_time']);

        $field_set = $field_group->getFieldSet('eedt_perf_alerts.form.max_query_time');
        $field_set->setDesc('eedt_perf_alerts.form.desc.form.max_query_time');
        $field = $field_set->getField('max_query_time', 'number');
        $field->setValue($settings['max_query_time']);

        return $form;
    }
}
