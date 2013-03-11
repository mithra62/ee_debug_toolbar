<?php
/**
 *
 *
 * @author Christopher Imrie
 */
class Eedt_panel_model
{

	/*
	|--------------------------------------------------------------------------
	| Defaults
	|--------------------------------------------------------------------------
	*/

	/**
	 * @var Eedt_panel_model::PANEL_POSITION|Eedt_panel_model::BEFORE_TOOLBAR_POSITION|Eedt_panel_model::AFTER_TOOLBAR_POSITION Panel view injection point relative to the toolbar
	 */
	private $injection_point = Eedt_panel_model::PANEL_POSITION;

	/**
	 * @var bool
	 */
	private $show_button = true;

	/**
	 * @var string
	 */
	private $target_prefix = "Eedt_debug_";

	private $target_suffix = "_panel";

	/*
	|--------------------------------------------------------------------------
	| Constants
	|--------------------------------------------------------------------------
	*/

	/**
	 * Injection Point Positions
	 */
	const PANEL_POSITION = 1;
	const BEFORE_TOOLBAR_POSITION = 2;
	const AFTER_TOOLBAR_POSITION = 3;


	/*
	|--------------------------------------------------------------------------
	| Instance Variables
	|--------------------------------------------------------------------------
	*/

	/**
	 * @var string Toolbar short name (used in CSS and JS targetting)
	 */
	private $name;

	/**
	 * @var string Toolbar button label
	 */
	private $button_label;

	/**
	 * @var string Toolbar button icon filename
	 */
	private $button_icon;

	/**
	 * @var string
	 */
	private $button_icon_alt_title;

	/**
	 * @var string Toolbar panel HTML output
	 */
	private $output;

	/**
	 * @var array JS resources needed by this toolbar view
	 */
	private $js = array();

	/**
	 * @var array JS resources needed by this toolbar view, to be loaded on page load
	 */
	private $page_load_js = array();

	/**
	 * @var array CSS resources needed by this toolbar view
	 */
	private $css = array();

	/**
	 * @var array CSS resources needed by this toolbar view, to be loaded on page load
	 */
	private $page_load_css = array();
	
	/**
	 * @var string URL endpoint for Ajax requests
	 */
	private $ajax_url = '';

	/**
	 * @var string
	 */
	private $panel_css_class = '';

	/**
	 * @param string $name
	 */
	public function set_name($name)
	{
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function get_name()
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function get_target()
	{
		return $this->target_prefix . $this->get_name() . $this->target_suffix;
	}

	/**
	 * @param string $label
	 */
	function set_button_label($label)
	{
		$this->button_label = $label;
	}

	/**
	 * @return string
	 */
	public function get_button_label()
	{
		return $this->button_label;
	}

	/**
	 * @param string $filename
	 */
	function set_button_icon($filename)
	{
		$this->button_icon = $filename;
	}

	/**
	 * @return string
	 */
	public function get_button_icon()
	{
		return $this->button_icon;
	}

	/**
	 * @param string $text
	 */
	public function set_button_icon_alt_text($text="")
	{
		$this->button_icon_alt_title = $text;
	}

	/**
	 * @return string
	 */
	public function get_button_icon_alt_text()
	{
		if($this->button_icon_alt_title){
			return $this->button_icon_alt_title;
		}

		return $this->get_button_label();
	}

	/**
	 * @param string $html
	 */
	function set_output($html = "")
	{
		$this->output = $html;
	}

	/**
	 * @return string
	 */
	function get_output()
	{
		return $this->output;
	}
	
	public function set_ajax_url($url)
	{
		$this->ajax_url = $url;
	}
	
	public function get_ajax_url()
	{
		return $this->ajax_url;
	}

	public function set_panel_css_class($css)
	{
		$this->panel_css = $css;
	}

	public function get_panel_css_class()
	{
		return $this->panel_css_class;
	}

	/**
	 * @param string $filename
	 * @param boolean $page_load
	 */
	function add_js($filename, $page_load = FALSE)
	{
		if($page_load) {
			$this->page_load_js[] = $filename;
			return;
		}
		$this->js[] = $filename;
	}

	/**
	 * @return array
	 */
	public function get_js()
	{
		return $this->js;
	}

	/**
	 * @return array
	 */
	public function get_page_load_js()
	{
		return $this->page_load_js;
	}

	/**
	 * @param string $filename
	 * @param boolean $page_load
	 */
	function add_css($filename, $page_load = FALSE)
	{
		if($page_load) {
			$this->page_load_css[] = $filename;
			return;
		}
		$this->css[] = $filename;
	}

	/**
	 * @return array
	 */
	public function get_css()
	{
		return $this->css;
	}

	/**
	 * @return array
	 */
	public function get_page_load_css()
	{
		return $this->page_load_css;
	}

	/**
	 * @param Eedt_panel_model::PANEL_POSITION|Eedt_panel_model::BEFORE_TOOLBAR_POSITION|Eedt_panel_model::AFTER_TOOLBAR_POSITION $injection_point
	 */
	function set_injection_point($injection_point = Eedt_panel_model::PANEL_POSITION)
	{
		$this->injection_point = $injection_point;
	}

	/**
	 * @param bool $enabled
	 */
	function set_button_display($enabled = true)
	{
		$this->show_button = $enabled;
	}
}
