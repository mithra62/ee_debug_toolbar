<?php

namespace Mithra62\DebugToolbar\Panels;

class Model
{
    /**
     * @var int
     */
    private int $injection_point = Model::PANEL_IN_TOOLBAR;

    /**
     * @var bool
     */
    private bool $show_button = true;

    /**
     * @var string
     */
    private string $target_prefix = "Eedt_debug_";

    private string $target_suffix = "_panel";

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

    /**
     * @var string Toolbar short name (used in CSS and JS targetting)
     */
    private string $name = '';

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
    private $page_load_js = [];

    /**
     * @var array CSS resources needed by this toolbar view
     */
    private $css = [];

    /**
     * @var array CSS resources needed by this toolbar view, to be loaded on page load
     */
    private $page_load_css = [];

    /**
     * @var string The URL endpoint for fetching panel HTML content when toolbar button is clicked
     */
    private string $panel_fetch_url = '';

    /**
     * @var string
     */
    private string $panel_css_class = '';

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getTarget()
    {
        return $this->target_prefix . $this->getName() . $this->target_suffix;
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
    public function setButtonIconAltText($text = "")
    {
        $this->button_icon_alt_title = $text;
    }

    /**
     * @return string
     */
    public function getButtonIconAltText()
    {
        if ($this->button_icon_alt_title) {
            return $this->button_icon_alt_title;
        }

        return $this->getButtonLabel();
    }

    /**
     * @param string $html
     */
    public function setPanelContents(string $html = ''): void
    {
        $this->output = $html;
    }

    /**
     * @return string
     */
    function getPanelContents(): string
    {
        return $this->output;
    }

    /**
     * @param string $css
     */
    public function setPanelCssClass($css)
    {
        $this->panel_css_class = $css;
    }

    /**
     * @return string
     */
    public function getPanelCssClass()
    {
        return $this->panel_css_class;
    }

    /**
     * @param string $filename
     * @param boolean $page_load
     */
    function addJs($filename, $page_load = FALSE)
    {
        if ($page_load) {
            $this->page_load_js[] = $filename;
            return;
        }
        $this->js[] = $filename;
    }

    /**
     * @return array
     */
    public function getJs()
    {
        return $this->js;
    }

    /**
     * @return array
     */
    public function getPageLoadJs()
    {
        return $this->page_load_js;
    }

    /**
     * @param string $filename
     * @param boolean $page_load
     */
    function addCss($filename, $page_load = FALSE)
    {
        if ($page_load) {
            $this->page_load_css[] = $filename;
            return;
        }
        $this->css[] = $filename;
    }

    /**
     * @return array
     */
    public function getCss()
    {
        return $this->css;
    }

    /**
     * @return array
     */
    public function getPageLoadCss()
    {
        return $this->page_load_css;
    }

    /**
     * @param int $injection_point
     * @return void
     */
    function setInjectionPoint(int $injection_point = Model::PANEL_IN_TOOLBAR)
    {
        $this->injection_point = $injection_point;
    }

    /**
     * @return int
     */
    function getInjectionPoint()
    {
        return $this->injection_point;
    }

    /**
     * @param bool $enabled
     */
    function setShowButton($enabled = true)
    {
        $this->show_button = $enabled;
    }

    /**
     * @return bool
     */
    function showButton()
    {
        return $this->show_button;
    }

    /**
     * @return string
     */
    public function getPanelFetchUrl()
    {
        return $this->panel_fetch_url;
    }

    /**
     * @param string $panel_fetch_url
     */
    public function setPanelFetch_url($panel_fetch_url)
    {
        $this->panel_fetch_url = str_replace("&amp;", "&", $panel_fetch_url);
    }
}
