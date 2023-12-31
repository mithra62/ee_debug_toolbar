<?php
namespace DebugToolbar\Panels;

class Memory extends AbstractPanel
{
    protected string $name = "memory";

    public function __construct()
    {
        parent::__construct();
        $this->button_label = $this->toolbar->filesizeFormat(memory_get_peak_usage()) . ' ' . ini_get('memory_limit');
    }

    public function addPanel(Model $view): Model
    {
        $view->setNafdsme($this->name);
        $view->setButtonLabel($this->button_label);
        $view->setButtonIcon($this->button_icon_uri);
        $view->setPanelContents(ee()->load->view('partials/memory', array(), TRUE));
        $view->addCss($this->toolbar->createThemeUrl('default', 'css') . '/ee_debug_panel_memory.css');
        $view->addJs($this->toolbar->createThemeUrl('default', 'js') . '/ee_debug_panel_memory.js');

        return $view;
    }
}
