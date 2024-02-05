<?php
namespace DebugToolbar\Panels;

use DebugToolbar\Panels\Model;

class Copyright extends AbstractPanel
{
    /**
     * @var string
     */
    protected string $name = "copyright";

    public function __construct()
    {
        parent::__construct();
        $this->button_label = 'v' . APP_VER . ' / ' . phpversion();
    }

    /**
     * @param \DebugToolbar\Panels\Model $view
     * @return \DebugToolbar\Panels\Model
     */
    public function addPanel(Model $view): Model
    {
        $data['project_contributors'] = $this->getContributors();
        $view->setPanelContents(ee()->load->view('partials/copyright', $data, true));
        $view = parent::addPanel($view);
        $toolbar = ee('ee_debug_toolbar:ToolbarService');
        $view->addCss($toolbar->createThemeUrl('default', 'css') . '/ee_debug_panel_copyright.css');


        return $view;
    }

    /**
     * @return array
     */
    protected function getContributors(): array
    {
        $data = [];
        $providers = ee('App')->getProviders();
        foreach($providers AS $provider)
        {
            if($provider->get('author') && $provider->get('author_url')) {
                $data[$provider->get('author')] = $provider->get('author_url');
            }
        }
        return $data;
    }
}
