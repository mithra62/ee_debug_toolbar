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
            'class' => 'Ee_debug_toolbar',
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
            ->filter('class', 'Ee_debug_toolbar')
            ->filter('method', 'PanelAjax')
            ->delete();
    }
}
