<?php

use ExpressionEngine\Service\Migration\Migration;

class Createeedebugsettingstable extends Migration
{
    /**
     * Execute the migration
     * @return void
     */
    public function up()
    {
        $fields = array(
            'id' => array('type' => 'int', 'constraint' => '10', 'unsigned' => true, 'auto_increment' => true),
            'site_id' => array('type' => 'int', 'constraint' => '4', 'unsigned' => true, 'default' => 1),
            'setting_key' => array('type' => 'varchar', 'constraint' => '30'),
            'setting_value' => array('type' => 'text'),
            'serialized' => array('type' => 'int', 'constraint' => '1', 'default' => null),
        );

        ee()->dbforge->add_field($fields);
        ee()->dbforge->add_key('id', true);
        ee()->dbforge->create_table('eedt_settings');
    }

    /**
     * Rollback the migration
     * @return void
     */
    public function down()
    {
        ee()->dbforge->drop_table('eedt_settings');
    }
}
