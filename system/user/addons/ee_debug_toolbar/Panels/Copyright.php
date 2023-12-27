<?php
namespace Mithra62\DebugToolbar\Panels;

use Mithra62\DebugToolbar\Panels\Model;

class Copyright extends AbstractPanel
{
    protected string $name = "copyright";

    public function __construct()
    {
        parent::__construct();
        $this->button_label = 'v' . APP_VER . ' / ' . phpversion();
    }

    public function addPanel(Model $view): Model
    {
        $view = parent::addPanel($view);
        $toolbar = ee('ee_debug_toolbar:ToolbarService');
        $view->addCss($toolbar->createThemeUrl('default', 'css') . '/ee_debug_panel_copyright.css');

        return $view;
    }
}
