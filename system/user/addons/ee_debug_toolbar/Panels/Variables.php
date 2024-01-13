<?php
namespace DebugToolbar\Panels;

class Variables extends AbstractPanel
{
    /**
     * @var string
     */
	protected string $name = "variables";

    public function addPanel(Model $view): Model
    {
        $view = parent::addPanel($view);
        $view->addJs($this->toolbar->createThemeUrl('default', 'js') . '/ee_debug_panel_variables.js');
        return $view;
    }
}
