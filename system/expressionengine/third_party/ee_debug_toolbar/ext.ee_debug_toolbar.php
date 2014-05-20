<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * mithra62 - EE Debug Toolbar
 *
 * @package        mithra62:EE_debug_toolbar
 * @author         Eric Lamb
 * @copyright      Copyright (c) 2013, mithra62, Eric Lamb.
 * @link           http://mithra62.com/
 * @updated        1.0
 * @filesource     ./system/expressionengine/third_party/ee_debug_toolbar/
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
	 * @var Object EE Super Global
	 */
	protected $EE;

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
	public $version = '';
	
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
	 * Where to get help
	 * 
	 * @var string
	 */
	public $docs_url = 'https://github.com/mithra62/ee_debug_toolbar/wiki';
	
	/**
	 * The full path to store the cached debug output
	 * @var string
	 */
	public $cache_dir = '';
	
	/**
	 * The order the default panels appear in.
	 * Also used to differentiate the native panels from third party panels
	 * @var array
	 */
	public $panel_order = array(
			'copyright', 
			'variables', 
			'files', 
			'memory', 
			'time', 
			'config',
			'database'
	);
	
	/**
	 * Flag to have module files handle updatting
	 * @var unknown_type
	 */
	public $required_by = array('module');
	
	/**
	 * List of methods available for use with EEDT ACT
	 * @var array
	 */
	public $eedt_act = array('get_panel_data', 'panel_ajax');


	public function __construct($settings = '')
	{
		$this->EE       =& get_instance();
		$this->EE->lang->loadfile('ee_debug_toolbar');
		$path = dirname(realpath(__FILE__));
		include $path.'/config'.EXT;
		$this->version = $config['version'];
		$this->name        = lang('ee_debug_toolbar_module_name');
		$this->description = lang('ee_debug_toolbar_module_description');
		$this->EE->load->add_package_path(PATH_THIRD . 'ee_debug_toolbar/');
		
		$this->cache_dir = APPPATH.'cache/eedt/';
		if(!is_dir($this->cache_dir))
		{
			mkdir($this->cache_dir, 0777, true);
		}
	}

	public function toolbar($session)
	{
		$session = ($this->EE->extensions->last_call != '' ? $this->EE->extensions->last_call : $session);

		//OK, this is kind of stupid, but CI only compiles debug data if both the profiler is on and the user is Super Admin.
		if ($this->EE->config->config['show_profiler'] != 'y' || $session->userdata('group_id') != '1') 
		{
			return $session;
		}

		//we don't want to compile Toolbar data on certain requests
		$ignore_controllers = array('javascript', 'css', 'content_files_modal');
		if (in_array($this->EE->input->get("C"), $ignore_controllers)) 
		{
			return $session;
		}
		
		//override to disable the toolbar from even starting
		if($this->EE->input->get('disable_toolbar') == 'yes')
		{		
			return $session;
		}

		global $EXT;

		//BELOW IS STOLEN FROM CHRIS IMRIE AND REQUIREJS WITH PERMISSION
		if (!class_exists('Ee_toolbar_hook')) 
		{
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
		//Attempt to patch the weird unfinished Active record chain (issue #18)
		$this->EE->db->limit(1)->get("channel_titles");
		
		//we have to check if the profiler and debugging is enabled again so other add-ons and templates can disable things if they want to
		//see Issue #48 for details (https://github.com/mithra62/ee_debug_toolbar/issues/48)
		if ($this->EE->config->config['show_profiler'] != 'y' || $this->EE->output->enable_profiler != '1')
		{
			return;
		}		

		$this->EE->load->file(PATH_THIRD . "ee_debug_toolbar/classes/Eedt_panel_model.php");
		$html = $this->EE->output->final_output;

		//If its an AJAX request (eg: EE JS Combo loader or jQuery library load) then call it a day...
		$ignore_tmpl_types = array('js', 'css');
		if (AJAX_REQUEST || (property_exists($this->EE, "TMPL") && in_array($this->EE->TMPL->template_type, $ignore_tmpl_types))) {
			return $this->EE->output->_display();
		}

		//starting a benchmark to make sure we're not a problem
		$this->EE->benchmark->mark('ee_debug_benchmark_start');

		$this->EE->load->library('Toolbar');
		$this->settings = $this->EE->toolbar->get_settings();
		
		//on 404 errors this can cause the data to get munged
		//to get around this, we only want to run the toolbar on certain pages
		///see $this->settings['profile_exts'] for details
		//NOTE: bookmarklets create an error for parse_url() so if it's a request for that we know we're good anyway so we disable check
		if($this->EE->input->get("tb_url") == '')
		{
			$url = parse_url($_SERVER['REQUEST_URI']);
			if(!empty($url['path']))
			{
				$parts = explode(".", $url['path'], 2);
				if(!empty($parts['1']))
				{
					if(in_array($parts['1'], $this->settings['profile_exts']))
					{				
						return $this->EE->output->_display();
					}
				}
			}
		}
		
		//Toolbar UI Vars
		$vars                                  = array();
		$vars['query_count']                   = $this->EE->db->query_count;
		$vars['mysql_query_cache']             = $this->EE->toolbar->verify_mysql_query_cache();
		$vars['elapsed_time']                  = $this->EE->benchmark->elapsed_time('total_execution_time_start', 'total_execution_time_end');
		$vars['config_data']                   = $this->EE->config->config;
		$vars['session_data']                  = $this->EE->session->all_userdata();
		$vars['query_data']                    = $this->EE->toolbar->setup_queries();
		$vars['memory_usage']                  = $this->EE->toolbar->filesize_format(memory_get_peak_usage());
		$vars['template_debugging_enabled']    = isset($this->EE->TMPL->log) && is_array($this->EE->TMPL->log) && count($this->EE->TMPL->log) > 0;
		$vars['template_debugging']            = ($vars['template_debugging_enabled'] ? $this->EE->toolbar->format_tmpl_log($this->EE->TMPL->log) : array());
		$vars['template_debugging_chart_json'] = ($vars['template_debugging_enabled'] ? $this->EE->toolbar->format_tmpl_chart_json($vars['template_debugging']) : array());
		$vars['included_file_data']            = $this->EE->toolbar->setup_files(get_included_files());

		$vars['ext_version']                   = $this->version;
		$this->settings                        = $this->EE->toolbar->get_settings();
		$vars['theme_img_url']                 = $this->EE->toolbar->create_theme_url($this->settings['theme'], 'images');
		$vars['theme_js_url']                  = $this->EE->toolbar->create_theme_url($this->settings['theme'], 'js');
		$vars['theme_css_url']                 = $this->EE->toolbar->create_theme_url($this->settings['theme'], 'css');
		$vars['extra_html']                    = ''; //used by extension to add extra script/css files
		$vars['eedt_theme_path']               = (defined('PATH_THIRD_THEMES') ? PATH_THIRD_THEMES : rtrim($this->EE->config->config['theme_folder_path'], '/third_party/') .'/').'ee_debug_toolbar/themes/'.$this->settings['theme'];
		$vars['master_view_script']            = "toolbar";
		$vars['panels']                        = array();
		$vars['toolbar_position']              = $this->determine_toolbar_position_class();
		$vars['js']                            = array($vars['theme_js_url'] . "eedt.js");
		$vars['css']                           = array($vars['theme_css_url']."ee_debug_toolbar.css");
		$vars['benchmark_data']                = array(); //we have to fake this for now

		//Load variables so that they are present in all view partials
		$this->EE->load->vars($vars);

		//Load Internal Panels & load view model data
		$panels = $this->load_panels();	
		$panel_data = array();	
		foreach($panels as $panel) 
		{
			$p = $panel->ee_debug_toolbar_add_panel(new Eedt_panel_model());
			$panel_data[$p->get_name()] = $p;
		}

		//Load third party panels and custom mods
		//yes, you can technically create panels in mod_panel but using 
		//add_panel will help future proof things
		if ($this->EE->extensions->active_hook('ee_debug_toolbar_add_panel') === true)
		{
			$panel_data = $this->EE->extensions->call('ee_debug_toolbar_add_panel', $panel_data, $vars);
		}
		
		//do... stuff... to panels... eventually...
		
		//apply custom modifications to toolbar
		//again, yes, you could create panels using mod_panel but you probably shouldn't ;)
		if ($this->EE->extensions->active_hook('ee_debug_toolbar_mod_panel') === true)
		{
			$panel_data = $this->EE->extensions->call('ee_debug_toolbar_mod_panel', $panel_data, $vars);
		}
		
		//have to verify the panels are good after letting the users have a go...
		foreach($panel_data AS $key => $panel)
		{
			if(!($panel instanceof Eedt_panel_model))
			{
				unset($panel_data[$key]);
				continue;
			}

			//If any panels have specified JS & CSS to be inserted on page load, collect them here
			$vars['css'] = array_merge($vars['css'], $panel->get_page_load_css());
			$vars['js'] = array_merge($vars['js'], $panel->get_page_load_js());
		}
		
		$vars['panels'] = $panel_data;
		$vars['js_config'] = $this->EE->toolbar->js_config($vars);
		
		//apply any customizations to the global view data 
		if ($this->EE->extensions->active_hook('ee_debug_toolbar_mod_view') === true)
		{
			$vars = $this->EE->extensions->call('ee_debug_toolbar_mod_view', $vars);
		}		

		//we have to "redo" the benchmark panel so we have all the internal benchmarks
		//COULD WREAK HAVOC ON BENCHMARK OVERRIDES!!!
		$this->EE->benchmark->mark('ee_debug_benchmark_end');
		$vars['benchmark_data'] = $this->EE->toolbar->setup_benchmarks();
		if(!empty($vars['panels']['time']))
		{
			$vars['panels']['time']->set_panel_contents($this->EE->load->view("partials/time", $vars, true));
		}

		//Break up the panels into the various injection points
		$vars['panels_before_toolbar'] = array();
		$vars['panels_in_toolbar'] = array();
		$vars['panels_after_toolbar'] = array();

		foreach($vars['panels'] as $panel) {
			switch ($panel->get_injection_point()) {
				case Eedt_panel_model::PANEL_BEFORE_TOOLBAR:
					$vars['panels_before_toolbar'][] = $panel;
					break;
				case Eedt_panel_model::PANEL_IN_TOOLBAR:
					$vars['panels_in_toolbar'][] = $panel;
					break;
				case Eedt_panel_model::PANEL_AFTER_TOOLBAR:
					$vars['panels_after_toolbar'][] = $panel;
					break;
			}
		}
		unset($vars['panels']);

		//setup the XML storage data for use by the panels on open
		$this->EE->toolbar->cache_panels($vars['panels_in_toolbar'], $this->cache_dir);
		
		//Render toolbar
		$toolbar_html = $this->EE->load->view($vars['master_view_script'], $vars, true);

		//Allow modification of final toolbar HTML output
		if ($this->EE->extensions->active_hook('ee_debug_toolbar_modify_output') === true)
		{
			$toolbar_html = $this->EE->extensions->call('ee_debug_toolbar_modify_output', $toolbar_html);
		}

		//Rare, but the closing body tag may not exist. So if it doesnt, append the template instead
		//of inserting. We may be able to get away with simply always appending, but this seems cleaner
		//even if more expensive.
		if (strpos($html, "</body>") === false)
		{
			$html .= $toolbar_html;
		}
		else 
		{
			$html = str_replace('</body>', $toolbar_html . '</body>', $html);
		}

		//Get CI to do its usual thing and build the final output, but we'll switch off the debugging
		//since we have already added the debug data to the body output. Doing it this way means
		//we should retain 100% compatibility (I'm looking at you Stash...)
		$this->EE->output->final_output = $html;
		if (isset($this->EE->TMPL)) 
		{
			$this->EE->TMPL->debugging = false;
			$this->EE->TMPL->log       = false;
		}
		
		$this->EE->output->enable_profiler = false;

		//Fist pump.
		$this->EE->output->_display();
	}


	/**
	 * Fetches cached panel HTML output
	 */
	public function get_panel_data()
	{	
		$panel = $this->EE->input->get('panel', false);
		if(!$panel)
		{
			return;
		}

		//the cache file is just an XML so we check for existance, node, and display. easy
		$this->EE->load->library('toolbar');
		$file = $this->cache_dir.$this->EE->toolbar->make_cache_filename().'.gz';
		if(file_exists($file) && is_readable($file))
		{
			$gz = gzfile($file);
			$gz = implode("", $gz);
			$xml = simplexml_load_string($gz);
			$panel_node = $panel.'_panel';
			if(isset($xml->panels->$panel_node->output) && $xml->panels->$panel_node->output != '')
			{
				echo base64_decode($xml->panels->$panel_node->output);
			}
			exit;
		}
	}

	/**
	 * Allows JS to communicate directly with a panel extension
	 */
	public function panel_ajax()
	{
		$data = array();
		$panel = $this->EE->input->get("panel", false);
		$method = $this->EE->input->get("method", false);

		if(!$panel || $method)
		{
			return;
		}

		if(in_array($panel, $this->panel_order)){
			//Native Panel
			$this->EE->load->file(PATH_THIRD.'ee_debug_toolbar/panels/Eedt_'.$panel.'_panel.php');
			$class = 'Eedt_'.$panel.'_panel';

			if(class_exists($class)){

				$instance = new $class();

				if(method_exists($instance, $method)){
					$data = $instance->$method();
				}
			}


		} else {
			//Third Party panel

			/**
			 * TODO
			 * I realise now that we need to somehow specify the path to the class since
			 * the panel name will not necessarily match up with the extension name, so we cant
			 * make that assumption.
			 */
		}

		if($data) {
			$this->EE->output->send_ajax_response($data);
		}
	}

	/**
	 * Loads Native EEDT Panel Extensions
	 *
	 * @return Eedt_base_panel[] Array of panel extension instances
	 */
	private function load_panels()
	{
		$instances = array();

		$this->EE->load->helper("file");
		$files = get_filenames(PATH_THIRD."ee_debug_toolbar/panels/");
		
		//setup the array in the order we want the panels to appear
		$sorted_files = array();
		foreach($this->panel_order AS $panel)
		{
			$name = 'Eedt_'.$panel.'_panel.php';
			if(in_array($name, $files))
			{
				$sorted_files[] = $name;
			}
		}
		
		//each panel is an object so set them up 
		foreach($sorted_files as $file){
			$this->EE->load->file(PATH_THIRD."ee_debug_toolbar/panels/" . $file);

			$class = str_replace(".php", "", $file);

			if(class_exists($class)){
				$instances[$class] = new $class();
			}
		}

		return $instances;
	}

	/**
	 * Determine the toolbar position classes to be added to the toolbar root node
	 * @return string
	 */
	private function determine_toolbar_position_class()
	{
		if(!array_key_exists("toolbar_position", $this->settings)){
			$this->settings['toolbar_position'] = 0;
		}

		switch ($this->settings['toolbar_position']) {
			case 1:
				$position = "top left";
				break;
			case 2:
				$position = "bottom right";
				break;
			case 3:
				$position = "top right";
				break;
			default:
				$position = "bottom left";
				break;
		}

		return $position;
	}
	
	public function settings()
	{
		$this->EE->functions->redirect(BASE.AMP.'C=addons_modules&M=show_module_cp&module=ee_debug_toolbar&method=settings');
	}
	
	public function activate_extension()
	{
		return true;
	}
	
	public function update_extension($current = '')
	{
		return true;
	}
	
	public function disable_extension()
	{
		return true;
	}	

}