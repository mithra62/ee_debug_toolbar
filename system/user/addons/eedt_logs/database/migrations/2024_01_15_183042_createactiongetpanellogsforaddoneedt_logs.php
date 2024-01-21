<?php

use ExpressionEngine\Service\Migration\Migration;

class CreateactiongetpanellogsforaddoneedtLogs extends Migration
{
    /**
     * Execute the migration
     * @return void
     */
    public function up()
    {
        ee('Model')->make('Action', [
            'class' => 'Eedt_logs',
            'method' => 'GetPanelLogs',
            'csrf_exempt' => false,
        ])->save();
    }

    /**
     * Rollback the migration
     * @return void
     */
    public function down()
    {
        ee('Model')->get('Action')
            ->filter('class', 'Eedt_logs')
            ->filter('method', 'GetPanelLogs')
            ->delete();
    }
}
