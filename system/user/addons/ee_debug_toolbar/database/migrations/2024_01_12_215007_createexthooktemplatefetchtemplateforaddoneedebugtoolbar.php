<?php

use ExpressionEngine\Service\Migration\Migration;

class CreateExtHookTemplateFetchTemplateForAddonEeDebugToolbar extends Migration
{
    /**
     * Execute the migration
     * @return void
     */
    public function up()
    {
        $addon = ee('Addon')->get('ee_debug_toolbar');

        $ext = [
            'class' => $addon->getExtensionClass(),
            'method' => 'template_fetch_template',
            'hook' => 'template_fetch_template',
            'settings' => serialize([]),
            'priority' => 10,
            'version' => $addon->getVersion(),
            'enabled' => 'y'
        ];

        // If we didnt find a matching Extension, lets just insert it
        ee('Model')->make('Extension', $ext)->save();
    }

    /**
     * Rollback the migration
     * @return void
     */
    public function down()
    {
        $addon = ee('Addon')->get('ee_debug_toolbar');

        ee('Model')->get('Extension')
            ->filter('class', $addon->getExtensionClass())
            ->delete();
    }
}
