<?php

use ExpressionEngine\Service\Migration\Migration;

class CreateMemoryHistoryTable extends Migration
{
    /**
     * Execute the migration
     * @return void
     */
    public function up()
    {
        $fields = [
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
                'unsigned' => true,
            ],
            'session_id' => [
                'type' => 'VARCHAR',
                'constraint' => '40',
                'null' => true,
            ],
            'url' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'peak_memory' => [
                'type' => 'FLOAT',
                'null' => true,
            ],
            'sql_count' => [
                'type' => 'INT',
                'null' => true,
            ],
            'execution_time' => [
                'type' => 'FLOAT',
                'null' => true,
            ],
            'timestamp' => [
                'type' => 'INT',
                'null' => true,
            ],
            'cp' => [
                'type' => 'ENUM',
                'constraint' => '\'y\',\'n\'',
                'default' => 'n',
                'null' => false,
            ],
        ];
        ee()->dbforge->add_field($fields);
        ee()->dbforge->add_key('id', true);
        ee()->dbforge->create_table('eedt_memory_history');
    }

    /**
     * Rollback the migration
     * @return void
     */
    public function down()
    {
        ee()->dbforge->drop_table('eedt_memory_history');
    }
}
