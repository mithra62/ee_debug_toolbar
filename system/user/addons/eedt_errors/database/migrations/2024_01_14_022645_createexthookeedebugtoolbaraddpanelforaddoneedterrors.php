<?php

use ExpressionEngine\Service\Migration\Migration;

class CreateExtHookEeDebugToolbarAddPanelForAddonEedtErrors extends Migration
{
    /**
     * Execute the migration
     * @return void
     */
    public function up()
    {
        $addon = ee('Addon')->get('eedt_errors');

        $ext = [
            'class' => 'Eedt_errors_ext',
            'method' => 'ee_debug_toolbar_add_panel',
            'hook' => 'eedt_add_panel',
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
        $addon = ee('Addon')->get('eedt_errors');

        ee('Model')->get('Extension')
            ->filter('class', 'Eedt_errors_ext')
            ->delete();
    }
}
