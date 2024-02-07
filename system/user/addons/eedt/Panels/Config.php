<?php
namespace DebugToolbar\Panels;

class Config extends AbstractPanel
{
    /**
     * @var string
     */
    protected string $name = "config";

    public function __construct()
    {
        parent::__construct();
        $this->button_label = lang($this->name) . ' (' . count(ee()->config->config) . ')';
    }
}
