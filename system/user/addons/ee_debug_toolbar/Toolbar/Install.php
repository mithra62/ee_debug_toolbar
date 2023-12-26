<?php
namespace Mithra62\DebugToolbar\Toolbar;

class Install
{
    public function __construct()
    {
        ee()->load->model('ee_debug_settings_model', 'debug_settings');
    }

    /**
     * Wrapper to install the toolbar
     *
     * Abstraction's for pussies
     *
     * @param str $extension
     * @param int $version
     * @return boolean
     */
    public function install($extension, $version)
    {
        $data = array(
            'class' => $extension,
            'method' => 'toolbar',
            'hook' => 'sessions_end',
            'settings' => serialize(array()),
            'priority' => 9999999,
            'version' => $version,
            'enabled' => 'y'
        );

        ee()->db->insert('extensions', $data);
        $this->add_settings_table();

        return TRUE;
    }

    /**
     * Wrapper to handle updates
     * @param str $class
     * @param int $version
     */
    public function update($class, $version, $current)
    {
        ee()->db->where('class', $class);
        ee()->db->update(
            'extensions',
            array('version' => $version)
        );

        if (version_compare($current, "0.9", '<')) {
            $this->add_settings_table();
        }
    }

    /**
     * Wrapper to remove the extension
     * @param str $extension
     */
    public function remove($extension)
    {
        ee()->load->dbforge();
        ee()->db->where('class', $extension);
        ee()->db->delete('extensions');

        ee()->dbforge->drop_table(ee()->debug_settings->settings_table);
    }

    private function add_settings_table()
    {
        ee()->load->dbforge();
        $fields = array(
            'id' => array(
                'type' => 'int',
                'constraint' => 10,
                'unsigned' => TRUE,
                'null' => FALSE,
                'auto_increment' => TRUE
            ),
            'setting_key' => array(
                'type' => 'varchar',
                'constraint' => '30',
                'null' => FALSE,
                'default' => ''
            ),
            'setting_value' => array(
                'type' => 'text',
                'null' => FALSE
            ),
            'serialized' => array(
                'type' => 'int',
                'constraint' => 1,
                'null' => TRUE,
                'default' => '0'
            )
        );

        ee()->dbforge->add_field($fields);
        ee()->dbforge->add_key('id', TRUE);
        ee()->dbforge->create_table(ee()->debug_settings->settings_table, TRUE);
    }
}