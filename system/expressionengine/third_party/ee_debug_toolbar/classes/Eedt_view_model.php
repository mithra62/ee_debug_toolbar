<?php
/**
 *
 *
 * @author Christopher Imrie
 */
class Eedt_view_model
{

	/*
	|--------------------------------------------------------------------------
	| Defaults
	|--------------------------------------------------------------------------
	*/

	/**
	 * @var Eedt_view_model::PANEL_POSITION|Eedt_view_model::BEFORE_TOOLBAR_POSITION|Eedt_view_model::AFTER_TOOLBAR_POSITION Panel view injection point relative to the toolbar
	 */
	private $injection_point = Eedt_view_model::PANEL_POSITION;

	/**
	 * @var bool
	 */
	private $show_button = true;

	/**
	 * @var string
	 */
	private $target_prefix = "EEDebug_";

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
	 * @var array CSS resources needed by this toolbar view
	 */
	private $css = array();
	
	/**
	 * @var string URL endpoint for Ajax requests
	 */
	private $ajax_url = '';
	
	/**
	 * @var container of panel info
	 */
	private $panels = array();
	
	private $panel_css = FALSE;


	/*
	|--------------------------------------------------------------------------
	| Methods
	|--------------------------------------------------------------------------
	*/


	/**
	 * @param string $name
	 */
	function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	function getName()
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	function getTarget()
	{
		return $this->target_prefix . $this->getName();
	}

	/**
	 * @param string $label
	 */
	function setButtonLabel($label)
	{
		$this->button_label = $label;
	}

	/**
	 * @return string
	 */
	public function getButtonLabel()
	{
		return $this->button_label;
	}

	/**
	 * @param string $filename
	 */
	function setButtonIcon($filename)
	{
		$this->button_icon = $filename;
	}

	/**
	 * @return string
	 */
	public function getButtonIcon()
	{
		return $this->button_icon;
	}

	/**
	 * @param string $text
	 */
	public function setButtonIconAltText($text="")
	{
		$this->button_icon_alt_title = $text;
	}

	/**
	 * @return string
	 */
	public function getButtonIconAltText()
	{
		if($this->button_icon_alt_title){
			return $this->button_icon_alt_title;
		}

		return $this->getButtonLabel();
	}

	/**
	 * @param string $html
	 */
	function setOuput($html = "")
	{
		$this->output = $html;
	}

	/**
	 * @return string
	 */
	function getOutput()
	{
		return $this->output;
	}
	
	public function setAjaxUrl($url)
	{
		$this->ajax_url = $url;
	}
	
	public function getAjaxUrl()
	{
		return $this->ajax_url;
	}
	
	public function setPanelCss($css)
	{
		$this->panel_css = $css;
	}
	
	public function getPanelCss()
	{
		return $this->panel_css;
	}	

	/**
	 * @param $filename
	 */
	function addJs($filename)
	{
		$this->js[] = $filename;
	}
	
	public function getJs()
	{
		return $this->js;
	}	

	/**
	 * @param $filename
	 */
	function addCss($filename)
	{
		$this->css[] = $filename;
	}
	
	public function getCss()
	{
		return $this->css;
	}

	/**
	 * @param Eedt_view_model::PANEL_POSITION|Eedt_view_model::BEFORE_TOOLBAR_POSITION|Eedt_view_model::AFTER_TOOLBAR_POSITION $injection_point
	 */
	function setInjectionPoint($injection_point = Eedt_view::PANEL_POSITION)
	{
		$this->injection_point = $injection_point;
	}

	/**
	 * @param bool $enabled
	 */
	function setButtonDisplay($enabled = true)
	{
		$this->show_button = $enabled;
	}
}
