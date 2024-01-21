<?php

use ExpressionEngine\Service\Migration\Migration;

class CreateactionfetchmemoryandsqlusageforaddoneedtMemoryHistory extends Migration
{
    /**
     * Execute the migration
     * @return void
     */
    public function up()
    {
        ee('Model')->make('Action', [
            'class' => 'Eedt_memory_history',
            'method' => 'FetchMemoryAndSqlUsage',
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
            ->filter('class', 'Eedt_memory_history')
            ->filter('method', 'FetchMemoryAndSqlUsage')
            ->delete();
    }
}
