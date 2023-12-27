<?php
namespace Mithra62\DebugToolbar\Panels;

use Mithra62\DebugToolbar\Services\ToolbarService;

class AbstractPanel
{
    /**
     * @var string Toolbar button icon file extension
     */
    private $button_icon_extension = ".png";

    /**
     * @var int Display order priority
     */
    protected $priority = 0;

    /**
     * @var string Panel Name
     */
    protected $name;

    /**
     * @var string Toolbar Button Label
     */
    protected $button_label;

    /**
     * @var string Toolbar Button Uri
     */
    protected $button_icon_uri;

    protected ToolbarService $toolbar;

    /*
    |--------------------------------------------------------------------------
    | Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Constructor
     */
    public function __construct()
    {
        ee()->load->helper("url");
        $this->toolbar = ee('ee_debug_toolbar:ToolbarService');
        $this->settings = $this->toolbar->getSettings();

        if (!$this->button_label) {
            $this->button_label = ucfirst(str_replace(array("_", "-"), " ", $this->name));
        }

        if (!$this->button_icon_uri) {
            $this->button_icon_uri = $this->toolbar->createThemeUrl($this->settings['theme'], 'images') . $this->name . $this->button_icon_extension;

        }
    }

    /**
     * Add Panel to Toolbar
     *
     * @param Eedt_view_model $view EEDT view object representing our panel and button
     * @return Eedt_view_model
     */
    public function ee_debug_toolbar_add_panel($view)
    {
        $view->set_name($this->name);
        $view->set_button_label($this->button_label);
        $view->set_button_icon($this->button_icon_uri);
        $view->set_panel_contents($this->view());

        return $view;
    }


    /**
     * Returns rendered view fragment as a string
     *
     * @param string $view_path
     * @param array $data
     * @return string
     */
    protected function view($view_path = "", $data = array())
    {
        if (!$view_path) {
            $view_path = 'partials/' . $this->name;
        }
        return ee()->load->view($view_path, $data, TRUE);
    }
}
