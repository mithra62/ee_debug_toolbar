<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

use ExpressionEngine\Service\Addon\Mcp;

class Ee_debug_toolbar_mcp extends Mcp
{
    protected $addon_name = 'ee_debug_toolbar';

    public function index()
    {
        return $this->route('settings');
    }
}