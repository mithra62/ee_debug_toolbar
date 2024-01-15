<?php

use ExpressionEngine\Service\Migration\Migration;

class CreateExtHookEeDebugToolbarAddPanelForAddonEedtLogs extends Migration
{
    /**
     * Execute the migration
     * @return void
     */
    public function up()
    {
        $addon = ee('Addon')->get('eedt_logs');

        $ext = [
            'class' => $addon->getExtensionClass(),
            'method' => 'ee_debug_toolbar_add_panel',
            'hook' => 'ee_debug_toolbar_add_panel',
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
        $addon = ee('Addon')->get('eedt_logs');

        ee('Model')->get('Extension')
            ->filter('class', $addon->getExtensionClass())
            ->delete();
    }
}
