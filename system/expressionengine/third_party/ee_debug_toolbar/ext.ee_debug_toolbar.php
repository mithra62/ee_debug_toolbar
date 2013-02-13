<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * mithra62 - EE Debug Toolbar
 *
 * @package        mithra62:EE_debug_toolbar
 * @author         Eric Lamb
 * @copyright      Copyright (c) 2012, mithra62, Eric Lamb.
 * @link           http://mithra62.com/
 * @updated        1.0
 * @filesource     ./system/expressionengine/third_party/nagger/
 */

/**
 * EE Debug Toolbar - Extension
 *
 * Extension class
 *
 * @package        mithra62:EE_debug_toolbar
 * @author         Eric Lamb
 * @filesource     ./system/expressionengine/third_party/ee_debug_toolbar/ext.ee_debug_toolbar.php
 */
class Ee_debug_toolbar_ext
{		
	/**
	 * The extensions default settings
	 *
	 * @var array
	 */
	public $settings = array(
			'theme' => 'default'
	);

	/**
	 * Persistent storage to hold settings across the
	 * multiple class initialisations by EE and then CI
	 * 
	 * @var array
	 */
	static $persistent_settings = array();

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
	public $version = '0.9.1';
	
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
	public $settings_exist = 'y';
	
	/**
	 * Where to get help (nowhere for now)
	 * 
	 * @var string
	 */
	public $docs_url = '';


	public function __construct($settings = '')
	{
		$this->EE       =& get_instance();
		$this->EE->lang->loadfile('ee_debug_toolbar');
		$this->name        = lang('ee_debug_toolbar_module_name');
		$this->description = lang('ee_debug_toolbar_module_description');
		$this->EE->load->add_package_path(PATH_THIRD . 'ee_debug_toolbar/');
	}

	public function toolbar($session)
	{
		$session = ($this->EE->extensions->last_call != '' ? $this->EE->extensions->last_call : $session);

		//OK, this is kind of stupid, but CI only compiles debug data if both the profiler is on and the user is Super Admin.
		if ($this->EE->config->config['show_profiler'] != 'y' || $session->userdata('group_id') != '1') {
			return $session;
		}

		if ($this->EE->input->get("C") == "javascript") {
			return $session;
		}

		global $EXT;

		//BELOW IS STOLEN FROM CHRIS IMRIE AND REQUIREJS WITH PERMISSION
		if (!class_exists('Ee_toolbar_hook')) {
			$this->EE->load->file(PATH_THIRD . "ee_debug_toolbar/libraries/Ee_toolbar_hook.php");
		}

		//We overwrite the CI_Hooks class with our own since the CI_Hooks class will always load
		//hooks class files relative to APPPATH, when what we really need is to load RequireJS hook from the
		//third_party folder, which we KNOW can always be found with PATH_THIRD. Hence we extend the class and
		//simply redefine the _run_hook method to load relative to PATH_THIRD. Simples.
		$EET_EXT = new Ee_toolbar_hook();

		//Capture existing hooks just in case (although this is EE - it's unlikely)
		$EET_EXT->hooks = $EXT->hooks;

		//Enable CI Hooks
		$EET_EXT->enabled = true;

		//Create the post_controller hook array if needed
		if (!isset($EET_EXT->hooks['post_controller'])) {
			$EET_EXT->hooks['post_controller'] = array();
		}

		//Add our hook
		$EET_EXT->hooks['display_override'][] = array(
			'class'    => __CLASS__,
			'function' => 'modify_output',
			'filename' => basename(__FILE__),
			'filepath' => "ee_debug_toolbar",
			'params'   => array()
		);


		//Overwrite the global CI_Hooks instance with our modified version
		$EXT = $EET_EXT;

		return $session;
	}

	/**
	 * Post EE Controller
	 *
	 * This method will be called after the EE Controller has finished.
	 *
	 * @return null
	 */
	public function modify_output()
	{
		//If its an AJAX request (eg: EE JS Combo loader or jQuery library load) then call it a day...
		if (AJAX_REQUEST || (property_exists($this->EE, "TMPL") && $this->EE->TMPL->template_type == 'js')) {
			return $this->EE->output->_display();
		}

		$this->EE->load->library('Toolbar');

		//starting a benchmark to make sure we're not a problem
		$this->EE->benchmark->mark('ee_debug_benchmark_start');

		//Toolbar UI Vars
		$vars                                  = array();
		$vars['query_count']                   = $this->EE->db->query_count;
		$vars['elapsed_time']                  = $this->EE->benchmark->elapsed_time('total_execution_time_start', 'total_execution_time_end');
		$vars['config_data']                   = $this->EE->config->config;
		$vars['session_data']                  = $this->EE->session->all_userdata();
		$vars['query_data']                    = $this->EE->toolbar->setup_queries();
		$vars['memory_usage']                  = $this->EE->toolbar->filesize_format(memory_get_peak_usage());
		$vars['template_debugging_enabled']    = isset($this->EE->TMPL->log) && is_array($this->EE->TMPL->log) && count($this->EE->TMPL->log) > 0;
		$vars['template_debugging']            = ($vars['template_debugging_enabled'] ? $this->EE->toolbar->format_tmpl_log($this->EE->TMPL->log) : array());
		$vars['template_debugging_chart_json'] = ($vars['template_debugging_enabled'] ? $this->EE->toolbar->format_tmpl_chart_json($vars['template_debugging']) : array());
		$vars['included_file_data']            = $this->EE->toolbar->setup_files(get_included_files());

		$vars['ext_version'] = $this->version;
		$this->EE->benchmark->mark('ee_debug_benchmark_end');
		$vars['benchmark_data'] = $this->EE->toolbar->setup_benchmarks();
		$this->settings = $this->EE->toolbar->get_settings();
		$vars['theme_img_url'] = $this->EE->toolbar->create_theme_url($this->settings['theme'], 'images');
		$vars['theme_js_url'] = $this->EE->toolbar->create_theme_url($this->settings['theme'], 'js');
		$vars['theme_css_url'] = $this->EE->toolbar->create_theme_url($this->settings['theme'], 'css');

		$html = $this->EE->output->final_output;

		//Rare, but the closing body tag may not exist. So if it doesnt, append the template instead
		//of inserting. We may be able to get away with simply always appending, but this seems cleaner
		//even if more expensive.
		if (strpos($html, "</body>") === false) {
			$html .= $this->EE->load->view('toolbar', $vars, true);
		} else {
			$html = str_replace('</body>', $this->EE->load->view('toolbar', $vars, true) . '</body>', $html);
		}


		//Get CI to do its usual thing and build the final output, but we'll switch off the debugging
		//since we have already added the debug data to the body output. Doing it this way means
		//we should retain 100% compatibility (I'm looking at you Stash...)
		$this->EE->output->final_output = $html;
		if (isset($this->EE->TMPL)) {
			$this->EE->TMPL->debugging = false;
			$this->EE->TMPL->log       = false;
		}
		$this->EE->output->enable_profiler = false;

		//Fist pump.
		$this->EE->output->_display();
	}
	
	public function settings_form()
	{
		$this->EE->load->library('toolbar');
		$this->settings = $this->EE->toolbar->get_settings();
		
		$vars = array();
		$vars['settings'] = $this->settings;
		$vars['available_themes'] = $this->EE->toolbar->get_themes();
		$vars['settings_disable'] = FALSE;
		if(isset($this->EE->config->config['ee_debug_toolbar']))
		{
			$vars['settings_disable'] = 'disabled="disabled"';
		}		
		
		return $this->EE->load->view('settings', $vars, TRUE);
	}	
	
	public function save_settings()
	{
		$this->EE->load->library('toolbar');
		$this->settings = $this->EE->toolbar->get_settings();
		if($this->EE->debug_settings->update_settings($_POST))
		{
			$this->EE->logger->log_action($this->EE->lang->line('log_settings_updated'));
			$this->EE->session->set_flashdata('message_success', $this->EE->lang->line('settings_updated'));
			$this->EE->functions->redirect('?D=cp&C=addons_extensions');
			exit;
		}
		else
		{
			$this->EE->session->set_flashdata('message_failure', $this->EE->lang->line('settings_update_fail'));
			$this->EE->functions->redirect('?D=cp&C=addons_extensions');
			exit;
		}		
	}

	public function activate_extension()
	{
		$this->EE->load->library('ee_toolbar_install', null, 'install');
		$this->EE->install->install(__CLASS__, $this->version);
	}

	public function update_extension($current = '')
	{
		if ($current == '' OR $current == $this->version) {
			return false;
		}

		$this->EE->load->library('ee_toolbar_install', null, 'install');
		$this->EE->install->update(__CLASS__, $this->version, $current);
	}

	public function disable_extension()
	{
		$this->EE->load->library('ee_toolbar_install', null, 'install');
		$this->EE->install->remove(__CLASS__);		
	}

}