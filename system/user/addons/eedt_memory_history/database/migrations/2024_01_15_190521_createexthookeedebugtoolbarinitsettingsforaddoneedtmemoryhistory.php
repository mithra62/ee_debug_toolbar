<?php

use ExpressionEngine\Service\Migration\Migration;

class CreateExtHookEeDebugToolbarInitSettingsForAddonEedtMemoryHistory extends Migration
{
    /**
     * Execute the migration
     * @return void
     */
    public function up()
    {
        $ext = [
            'class' => 'Eedt_memory_history_ext',
            'method' => 'ee_debug_toolbar_init_settings',
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
        ee('Model')->get('Extension')
            ->filter('class', 'Eedt_memory_history_ext')
            ->delete();
    }
}
