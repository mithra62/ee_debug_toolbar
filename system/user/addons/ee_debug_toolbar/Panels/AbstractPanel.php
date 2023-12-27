<?php
namespace Mithra62\DebugToolbar\Panels;

use Mithra62\DebugToolbar\Services\ToolbarService;
use Mithra62\DebugToolbar\Panels\Model;

class AbstractPanel
{
    /**
     * @var string Toolbar button icon file extension
     */
    protected string $button_icon_extension = ".png";

    /**
     * @var int Display order priority
     */
    protected int $priority = 0;

    /**
     * @var string Panel Name
     */
    protected string $name = '';

    /**
     * @var string Toolbar Button Label
     */
    protected string $button_label = '';

    /**
     * @var string Toolbar Button Uri
     */
    protected string $button_icon_uri = '';

    /**
     * @var ToolbarService|mixed
     */
    protected ToolbarService $toolbar;

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


    /**
     * @param \Mithra62\DebugToolbar\Panels\Model $view
     * @return \Mithra62\DebugToolbar\Panels\Model
     */
    public function addPanel(Model $view): Model
    {
        $view->setName($this->name);
        $view->setButtonLabel($this->button_label);
        $view->setButtonIcon($this->button_icon_uri);
        $view->setPanelContents($this->view());

        return $view;
    }


    /**
     * Returns rendered view fragment as a string
     *
     * @param string $view_path
     * @param array $data
     * @return string
     */
    protected function view(string $view_path = '', array $data = [])
    {
        if (!$view_path) {
            $view_path = 'partials/' . $this->name;
        }
        return ee()->load->view($view_path, $data, TRUE);
    }
}
