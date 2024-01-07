<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

use ExpressionEngine\Service\Addon\Extension;

class Eedt_memory_history_ext extends Extension
{
    /**
     * @var string
     */
    protected $addon_name = 'eedt_memory_history';

    /**
     * Allowed methods that can be called via eedt.ajax()
     *
     * @var array
     */
    public array $eedt_act = [
        'fetch_memory_and_sql_usage',
    ];

    /**
     * AJAX Endpoint for JSON data
     *
     * Return array of performance data
     *
     */
    public function fetch_memory_and_sql_usage()
    {
        $session_id = ee()->session->userdata['session_id'];
        $is_cp = ee()->input->get('cp') == 'y' ? 'y' : 'n';
        $data = ee()->db->where("session_id", $session_id)
            ->where('cp', $is_cp)
            ->limit(20)
            ->order_by("timestamp", "desc")
            ->get("eedt_memory_history")
            ->result_array();

        //Garbage collect
        ee()->db->where("timestamp < ", ee()->localize->now - 14400)->delete("eedt_memory_history"); //4 hours
        ee()->output->send_ajax_response($data);
    }

    public function activate_extension()
    {
        ee()->load->dbforge();
        ee()->dbforge->drop_table('eedt_memory_history');

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

        $data = [];
        $data[] = [
            'class' => __CLASS__,
            'method' => 'ee_debug_toolbar_add_panel',
            'hook' => 'ee_debug_toolbar_add_panel',
            'settings' => '',
            'priority' => 49,
            'version' => DEBUG_TOOLBAR_MEMORY_HISTORY_VERSION,
            'enabled' => 'y',
        ];

        $data[] = [
            'class' => __CLASS__,
            'method' => 'ee_debug_toolbar_settings_form',
            'hook' => 'ee_debug_toolbar_settings_form',
            'settings' => '',
            'priority' => 1,
            'version' => DEBUG_TOOLBAR_MEMORY_HISTORY_VERSION,
            'enabled' => 'y',
        ];

        $data[] = [
            'class' => __CLASS__,
            'method' => 'ee_debug_toolbar_init_settings',
            'hook' => 'ee_debug_toolbar_init_settings',
            'settings' => '',
            'priority' => 5,
            'version' => DEBUG_TOOLBAR_MEMORY_HISTORY_VERSION,
            'enabled' => 'y',
        ];

        foreach ($data as $ext) {
            ee()->db->insert('extensions', $ext);
        }
        return true;
    }

    public function update_extension($current = '')
    {
        if ($current == '' or $current == DEBUG_TOOLBAR_MEMORY_HISTORY_VERSION) {
            return false;
        }

        ee()->db->where('class', __CLASS__);
        ee()->db->update(
            'extensions',
            ['version' => DEBUG_TOOLBAR_MEMORY_HISTORY_VERSION]
        );
    }

    public function disable_extension()
    {
        ee()->db->where('class', __CLASS__);
        ee()->db->delete('extensions');

        ee()->load->dbforge();
        ee()->dbforge->drop_table('eedt_memory_history');
    }

}