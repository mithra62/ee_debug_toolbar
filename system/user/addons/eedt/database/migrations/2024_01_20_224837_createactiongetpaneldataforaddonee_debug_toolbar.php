<?php

use ExpressionEngine\Service\Migration\Migration;

class CreateactiongetpaneldataforaddoneeDebugToolbar extends Migration
{
    /**
     * Execute the migration
     * @return void
     */
    public function up()
    {
        ee('Model')->make('Action', [
            'class' => 'Eedt',
            'method' => 'GetPanelData',
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
            ->filter('method', 'GetPanelData')
            ->delete();
    }
}
