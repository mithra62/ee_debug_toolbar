<?php

use ExpressionEngine\Service\Migration\Migration;

class CreateExtHookEmailSendForAddonEedtEmail extends Migration
{
    /**
     * Execute the migration
     * @return void
     */
    public function up()
    {
        $ext = [
            'class' => 'Eedt_email_ext',
            'method' => 'email_send',
            'hook' => 'email_send',
            'settings' => serialize([]),
            'priority' => 1,
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
            ->filter('class', 'Eedt_email_ext')
            ->delete();
    }
}
