<?php

use ExpressionEngine\Service\Migration\Migration;

class CreateactionclearerrorlogforaddoneedtErrors extends Migration
{
    /**
     * Execute the migration
     * @return void
     */
    public function up()
    {
        ee('Model')->make('Action', [
            'class' => 'Eedt_errors',
            'method' => 'ClearErrorLog',
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
            ->filter('class', 'Eedt_errors')
            ->filter('method', 'ClearErrorLog')
            ->delete();
    }
}
