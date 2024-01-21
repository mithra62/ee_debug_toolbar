<?php
namespace DebugToolbar\Panels;

class Files extends AbstractPanel
{
	protected string $name = "files";

	public function __construct()
	{
		parent::__construct();
		$this->button_label = count(get_included_files()).' '.lang('files');
	}

    public function addPanel(Model $view): Model
    {
        $view = parent::addPanel($view);
        $view->addJs($this->toolbar->createThemeUrl('default', 'js') . '/ee_debug_panel_files.js');
        return $view;
    }
}
