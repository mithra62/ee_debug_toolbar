<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * mithra62 - EE Debug Toolbar
 *
 * @package        mithra62:EE_debug_toolbar
 * @author         Christopher Imrie
 * @copyright      Copyright (c) 2013, Christopher Imrie.
 * @link           http://mithra62.com/
 * @updated        1.0
 * @filesource     ./system/expressionengine/third_party/ee_debug_toolbar/
 */

/**
 * Panel Model
 *
 * Panel API Model
 *
 * @package        mithra62:EE_debug_toolbar
 * @author         Christopher Imrie
 * @filesource     ./system/expressionengine/third_party/ee_debug_toolbar/classes/Eedt_panel_model.php
 */
class Eedt_panel_model
{

	/*
	|--------------------------------------------------------------------------
	| Defaults
	|--------------------------------------------------------------------------
	*/

	/**
	 * @var Eedt_panel_model::PANEL_IN_TOOLBAR|Eedt_panel_model::PANEL_BEFORE_TOOLBAR|Eedt_panel_model::PANEL_AFTER_TOOLBAR Panel view injection point relative to the toolbar
	 */
	private $injection_point = Eedt_panel_model::PANEL_IN_TOOLBAR;

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
	const PANEL_IN_TOOLBAR = 1;
	const PANEL_BEFORE_TOOLBAR = 2;
	const PANEL_AFTER_TOOLBAR = 3;


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
	 * @var string The URL endpoint for fetching panel HTML content when toolbar button is clicked
	 */
	private $panel_fetch_url = '';

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
	function set_panel_contents($html = "")
	{
		$this->output = $html;
	}

	/**
	 * @return string
	 */
	function get_panel_contents()
	{
		return $this->output;
	}

	/**
	 * @param string $css
	 */
	public function set_panel_css_class($css)
	{
		$this->panel_css_class = $css;
	}

	/**
	 * @return string
	 */
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
	 * @param Eedt_panel_model::PANEL_IN_TOOLBAR|Eedt_panel_model::PANEL_BEFORE_TOOLBAR|Eedt_panel_model::PANEL_AFTER_TOOLBAR $injection_point
	 */
	function set_injection_point($injection_point = Eedt_panel_model::PANEL_IN_TOOLBAR)
	{
		$this->injection_point = $injection_point;
	}

	/**
	 * @return Eedt_panel_model::PANEL_IN_TOOLBAR|Eedt_panel_model::PANEL_BEFORE_TOOLBAR|Eedt_panel_model::PANEL_AFTER_TOOLBAR $injection_point
	 */
	function get_injection_point()
	{
		return $this->injection_point;
	}

	/**
	 * @param bool $enabled
	 */
	function set_show_button($enabled = true)
	{
		$this->show_button = $enabled;
	}

	/**
	 * @return bool
	 */
	function show_button()
	{
		return $this->show_button;
	}

	/**
	 * @return string
	 */
	public function get_panel_fetch_url()
	{
		return $this->panel_fetch_url;
	}

	/**
	 * @param string $panel_fetch_url
	 */
	public function set_panel_fetch_url($panel_fetch_url)
	{
		$this->panel_fetch_url = str_replace("&amp;", "&", $panel_fetch_url);
	}
}
