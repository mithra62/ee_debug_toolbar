<?php
namespace Mithra62\DebugToolbar\Panels;

class Copyright extends AbstractPanel
{
    protected $name = "copyright";

    public function __construct()
    {
        parent::__construct();
        $this->button_label = 'v' . APP_VER . ' / ' . phpversion();
    }

    public function ee_debug_toolbar_add_panel($view)
    {
        $view = parent::ee_debug_toolbar_add_panel($view);
        $toolbar = ee('ee_debug_toolbar:ToolbarService');
        $view->add_css($toolbar->createThemeUrl('default', 'css') . '/ee_debug_panel_copyright.css');

        return $view;
    }
}
