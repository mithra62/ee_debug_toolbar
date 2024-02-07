<?php

use ExpressionEngine\Service\Migration\Migration;

class CreateactionactforaddoneeDebugToolbar extends Migration
{
    /**
     * Execute the migration
     * @return void
     */
    public function up()
    {
        ee('Model')->make('Action', [
            'class' => 'Eedt',
            'method' => 'Act',
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
            ->filter('class', 'Eedt')
            ->filter('method', 'Act')
            ->delete();
    }
}
