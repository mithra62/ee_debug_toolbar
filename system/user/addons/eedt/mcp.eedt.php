<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

use ExpressionEngine\Service\Addon\Mcp;

class Eedt_mcp extends Mcp
{
    protected $addon_name = 'eedt';

    public function index()
    {
        return $this->route('settings');
    }
}