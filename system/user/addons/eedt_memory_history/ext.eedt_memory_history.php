<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * mithra62 - EE Debug Toolbar
 *
 * @package        mithra62:EE_debug_toolbar
 * @author         Eric Lamb
 * @copyright      Copyright (c) 2012, mithra62, Eric Lamb.
 * @link           http://mithra62.com/
 * @updated        1.0
 * @filesource     ./system/expressionengine/third_party/eedt_memory_history/
 */

/**
 * EE Debug Toolbar - Memory History Extension
 *
 * Extension class
 *
 * @package        mithra62:EE_debug_toolbar
 * @author         Eric Lamb
 * @filesource     ./system/expressionengine/third_party/eedt_memory_history/ext.eedt_memory_history.php
 */
class Eedt_memory_history_ext
{
	/**
	 * The extension name
	 *
	 * @var string
	 */
	public $name = '';

	/**
	 * The extension version
	 *
	 * @var float
	 */
	public $version = '1.0';

	/**
	 * Used nowhere and not really needed (ya hear me ElisLab?!?!)
	 *
	 * @var string
	 */
	public $description = '';

	/**
	 * We're doing our own settings now so set this to off.
	 *
	 * @var string
	 */
	public $settings_exist = 'n';

	/**
	 * Where to get help (nowhere for now)
	 *
	 * @var string
	 */
	public $docs_url = 'https://github.com/mithra62/ee_debug_toolbar/wiki/Memory-History';

	/**
	 * Allowed methods that can be called via eedt.ajax()
	 *
	 * @var array
	 */
	public $eedt_act = array('fetch_memory_and_sql_usage');

	/**
	 * Default settings
	 *
	 * @var array
	 */
	public $default_settings = array(
		'memory_history_position' => "top right",
	);

	public function __construct($settings = '')
	{
		$this->EE       =& get_instance();
		$this->EE->lang->loadfile('eedt_memory_history');
		$this->name        = lang('eedt_memory_history_module_name');
		$this->description = lang('eedt_memory_history_module_description');
		$this->EE->load->add_package_path(PATH_THIRD . 'ee_debug_toolbar/');
		$this->EE->load->add_package_path(PATH_THIRD . 'eedt_memory_history/');
	}

	/**
	 * Add panel to UI & track memory and SQL usage
	 *
	 * @param Eedt_panel_model[] $panels
	 * @param array              $vars
	 * @return Eedt_panel_model[]
	 */
	public function ee_debug_toolbar_add_panel($panels = array(), $vars = array())
	{
		$this->EE->benchmark->mark('eedt_memory_history_start');
		$panels = ($this->EE->extensions->last_call != '' ? $this->EE->extensions->last_call : $panels);
		$settings = $this->EE->toolbar->get_settings();

		$panels['memory_history'] = new Eedt_panel_model();
		$panels['memory_history']->set_name("memory_history");
		$panels['memory_history']->set_panel_contents($this->EE->load->view('memory_history', array('position' => $settings['memory_history_position']), true));
		$panels['memory_history']->add_js('https://www.google.com/jsapi', true);
		$panels['memory_history']->add_js(eedt_theme_url() . 'eedt_memory_history/js/memory_history.js', true);
		$panels['memory_history']->add_css(eedt_theme_url() . 'eedt_memory_history/css/memory_history.css', true);
		$panels['memory_history']->set_injection_point(Eedt_panel_model::PANEL_AFTER_TOOLBAR);

		$this->track_memory_and_sql_usage($vars);

		$this->EE->benchmark->mark('eedt_memory_history_end');
		return $panels;
	}

	public function ee_debug_toolbar_init_settings($default_settings)
	{
		$default_settings = ($this->EE->extensions->last_call != '' ? $this->EE->extensions->last_call : $default_settings);
		return array_merge($default_settings, $this->default_settings);
	}

	public function ee_debug_toolbar_settings_form()
	{
		$settings = $this->EE->toolbar->get_settings();
		$settings_disable = false;
		if(isset($this->EE->config->config['ee_debug_toolbar']))
		{
			$settings_disable = 'disabled="disabled"';
		}

		$options = array(
			'bottom left' => 'bottom-left',
			'top left' => 'top-left',
			'top right' => 'top-right',
			'bottom right' => 'bottom-right'
		);
		
		$this->EE->table->add_row('<label for="memory_history_position">'.lang('memory_history_position')."</label><div class='subtext'>".lang('memory_history_position_instructions')."</div>", form_dropdown('memory_history_position',  $options, $settings['memory_history_position'], 'id="memory_history_position"'. $settings_disable));
	}


	/**
	 * Track memory and SQL performance
	 *
	 * @param string $html
	 * @return string mixed
	 */
	public function track_memory_and_sql_usage($vars)
	{
		$data = array(
			'session_id'  => $this->EE->session->userdata['session_id'],
			'url'         => $_SERVER["REQUEST_URI"] . $_SERVER["QUERY_STRING"],
			'peak_memory' => (float)$vars['memory_usage'],
			'sql_count'   => $vars['query_count'],
			'execution_time'   => $vars['elapsed_time'],
			'timestamp'   => $this->EE->localize->now,
			'cp'		  => $this->EE->input->get('D') == 'cp' ? 'y' : 'n'
		);
		$this->EE->db->insert('eedt_memory_history', $data);
	}

	/**
	 * AJAX Endpoint for JSON data
	 *
	 * Return array of performance data
	 *
	 */
	public function fetch_memory_and_sql_usage()
	{
		$session_id = $this->EE->session->userdata['session_id'];
		$is_cp = $this->EE->input->get('cp') == 'y' ? 'y' : 'n';
		$data = $this->EE->db->where("session_id", $session_id)
							 ->where('cp', $is_cp)
							 ->limit(20)
							 ->order_by("timestamp", "desc")
							 ->get("eedt_memory_history")
							 ->result_array();

		//Garbage collect
		$this->EE->db->where("timestamp < ", $this->EE->localize->now - 14400)->delete("eedt_memory_history"); //4 hours
		$this->EE->output->send_ajax_response($data);
	}

	public function activate_extension()
	{
		$this->EE->load->dbforge();
		$this->EE->dbforge->drop_table('eedt_memory_history');

		$fields = array(
			'id'          => array(
				'type'           => 'INT',
				'auto_increment' => true,
				'unsigned'       => true
			),
			'session_id'  => array(
				'type'       => 'VARCHAR',
				'constraint' => '40',
				'null'       => true
			),
			'url'         => array(
				'type'       => 'VARCHAR',
				'constraint' => '255',
				'null'       => true
			),
			'peak_memory' => array(
				'type' => 'FLOAT',
				'null' => true
			),
			'sql_count'   => array(
				'type' => 'INT',
				'null' => true
			),
			'execution_time'   => array(
				'type' => 'FLOAT',
				'null' => true
			),
			'timestamp'   => array(
				'type' => 'INT',
				'null' => true
			),
			'cp' => array(
				'type' => 'ENUM',
				'constraint' => '\'y\',\'n\'',
				'default' => 'n',
				'null' => false
			)
		);
		$this->EE->dbforge->add_field($fields);
		$this->EE->dbforge->add_key('id', true);
		$this->EE->dbforge->create_table('eedt_memory_history');

		$data = array();
		$data[] = array(
			'class'     => __CLASS__,
			'method'    => 'ee_debug_toolbar_add_panel',
			'hook'      => 'ee_debug_toolbar_add_panel',
			'settings'  => '',
			'priority'  => 49,
			'version'   => $this->version,
			'enabled'   => 'y'
		);

		$data[] = array(
			'class'     => __CLASS__,
			'method'    => 'ee_debug_toolbar_settings_form',
			'hook'      => 'ee_debug_toolbar_settings_form',
			'settings'  => '',
			'priority'  => 1,
			'version'   => $this->version,
			'enabled'   => 'y'
		);

		$data[] = array(
			'class'     => __CLASS__,
			'method'    => 'ee_debug_toolbar_init_settings',
			'hook'      => 'ee_debug_toolbar_init_settings',
			'settings'  => '',
			'priority'  => 5,
			'version'   => $this->version,
			'enabled'   => 'y'
		);

		foreach($data AS $ext)
		{
			$this->EE->db->insert('extensions', $ext);
		}
		return true;
	}

	public function update_extension($current = '')
	{
	    if ($current == '' OR $current == $this->version)
	    {
	        return false;
	    }

	    $this->EE->db->where('class', __CLASS__);
	    $this->EE->db->update(
	                'extensions',
	                array('version' => $this->version)
	    );
	}

	public function disable_extension()
	{
	    $this->EE->db->where('class', __CLASS__);
	    $this->EE->db->delete('extensions');
	    
	    $this->EE->load->dbforge();
	    $this->EE->dbforge->drop_table('eedt_memory_history');
	}

}