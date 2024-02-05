<?php

use ExpressionEngine\Service\Migration\Migration;

class CreateactionpanelajaxforaddoneeDebugToolbar extends Migration
{
    /**
     * Execute the migration
     * @return void
     */
    public function up()
    {
        ee('Model')->make('Action', [
            'class' => 'Eedt',
            'method' => 'PanelAjax',
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
            ->filter('method', 'PanelAjax')
            ->delete();
    }
}
