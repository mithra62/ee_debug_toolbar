<?php
namespace Mithra62\DebugToolbar\Panels;

class Memory extends AbstractPanel
{
    protected string $name = "memory";

    public function __construct()
    {
        parent::__construct();
        $this->button_label = $this->toolbar->filesizeFormat(memory_get_peak_usage()) . ' ' . ini_get('memory_limit');
    }


    public function ee_debug_toolbar_add_panel($view)
    {
        $view->set_name($this->name);
        $view->set_button_label($this->button_label);
        $view->set_button_icon($this->button_icon_uri);
        $view->set_panel_contents(ee()->load->view('partials/memory', array(), TRUE));
        $view->add_css($this->toolbar->createThemeUrl('default', 'css') . '/ee_debug_panel_memory.css');
        $view->add_js($this->toolbar->createThemeUrl('default', 'js') . '/ee_debug_panel_memory.js');

        return $view;
    }
}
