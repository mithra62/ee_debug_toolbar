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
        $ext = [
            'class' => 'Eedt_ext',
            'method' => 'template_fetch_template',
            'hook' => 'template_fetch_template',
            'settings' => serialize([]),
            'priority' => 10,
            'version' => DEBUG_TOOLBAR_VERSION,
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
        $addon = ee('Addon')->get('eedt');

        ee('Model')->get('Extension')
            ->filter('class', 'Eedt_ext')
            ->delete();
    }
}
