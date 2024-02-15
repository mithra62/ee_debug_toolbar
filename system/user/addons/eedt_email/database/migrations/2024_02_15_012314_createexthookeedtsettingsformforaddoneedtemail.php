<?php

use ExpressionEngine\Service\Migration\Migration;

class CreateExtHookEedtSettingsFormForAddonEedtEmail extends Migration
{
    /**
     * Execute the migration
     * @return void
     */
    public function up()
    {
        $addon = ee('Addon')->get('eedt_email');

        $ext = [
            'class' => $addon->getExtensionClass(),
            'method' => 'eedt_settings_form',
            'hook' => 'eedt_settings_form',
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
        $addon = ee('Addon')->get('eedt_email');

        ee('Model')->get('Extension')
            ->filter('class', $addon->getExtensionClass())
            ->delete();
    }
}
