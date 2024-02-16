<?php

use ExpressionEngine\Service\Migration\Migration;

class CreateExtHookEedtInitSettingsForAddonEedtEmail extends Migration
{
    /**
     * Execute the migration
     * @return void
     */
    public function up()
    {
        $addon = ee('Addon')->get('eedt_email');

        $ext = [
            'class' => 'Eedt_email_ext',
            'method' => 'eedt_init_settings',
            'hook' => 'eedt_init_settings',
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
        $addon = ee('Addon')->get('eedt_email');

        ee('Model')->get('Extension')
            ->filter('class', 'Eedt_email_ext')
            ->delete();
    }
}
