<?php
namespace Mithra62\DebugToolbar\Panels;

class Config extends AbstractPanel
{
    protected $name = "config";

    public function __construct()
    {
        parent::__construct();
        $this->button_label = lang($this->name) . ' (' . count(ee()->config->config) . ')';
    }
}