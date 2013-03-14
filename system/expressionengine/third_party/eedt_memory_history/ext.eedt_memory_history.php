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
	public $version = '0.1';
	
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
	public $docs_url = '';

	/**
	 * Allowed methods that can be called via eedt.axax()
	 * @var array
	 */
	public $eedt_act = array('fetch_memory_and_sql_usage');

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


		$panels['memory_history'] = new Eedt_panel_model();
		$panels['memory_history']->set_name("memory_history");
		$panels['memory_history']->set_panel_contents($this->EE->load->view('memory_history', array(), true));
		$panels['memory_history']->add_js('https://www.google.com/jsapi', true);
		$panels['memory_history']->add_js(URL_THIRD_THEMES . 'eedt_memory_history/js/memory_history.js', true);
		$panels['memory_history']->add_css(URL_THIRD_THEMES . 'eedt_memory_history/css/memory_history.css', true);
		$panels['memory_history']->set_injection_point(Eedt_panel_model::PANEL_AFTER_TOOLBAR);

		$this->track_memory_and_sql_usage($vars);

		$this->EE->benchmark->mark('eedt_memory_history_end');
		return $panels;
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
			'peak_memory' => $vars['memory_usage'],
			'sql_count'   => $vars['query_count'],
			'timestamp'   => $this->EE->localize->now
		);
		$this->EE->db->insert('eedt_memory_history', $data);
	}


	public function fetch_memory_and_sql_usage()
	{
		$session_id = $this->EE->session->userdata['session_id'];
		$data = $this->EE->db->where("session_id", $session_id)
							 ->limit(15)
							 ->order_by("timestamp", "desc")
							 ->get("eedt_memory_history")
							 ->result_array();

		//Garbage collect
		$this->EE->db->where("timestamp < ", $this->EE->localize->now - 14400); //4 hours


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
			'timestamp'   => array(
				'type' => 'INT',
				'null' => true
			)
		);
		$this->EE->dbforge->add_field($fields);
		$this->EE->dbforge->add_key('id', true);
		$this->EE->dbforge->create_table('eedt_memory_history');


		$data = array(
				'class'     => __CLASS__,
				'method'    => 'ee_debug_toolbar_add_panel',
				'hook'      => 'ee_debug_toolbar_add_panel',
				'settings'  => '',
				'priority'  => 49,
				'version'   => $this->version,
				'enabled'   => 'y'
		);
		
		$this->EE->db->insert('extensions', $data);
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
	}

}