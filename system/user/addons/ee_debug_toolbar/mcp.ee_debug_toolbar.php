<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

use ExpressionEngine\Service\Addon\Mcp;

class Ee_debug_toolbar_mcp extends Mcp
{
    protected $addon_name = 'ee_debug_toolbar';

    public function index()
    {
        return $this->route('settings');
    }

    public function sefdsattings()
    {
        ee()->load->library('table');
        $this->settings = ee('ee_debug_toolbar:ToolbarService')->getSettings();

        if (isset($_POST['go_settings']) && $_POST['go_settings'] == 'yes') {
            if (ee()->debug_settings->update_settings($_POST)) {
                //ee()->logger->log_ee()ion(ee()->lang->line('log_settings_updated'));
                ee()->session->set_flashdata('message_success', ee()->lang->line('settings_updated'));
                ee()->functions->redirect($this->url_base . 'settings');
                exit;
            } else {
                ee()->session->set_flashdata('message_failure', ee()->lang->line('settings_update_fail'));
                ee()->functions->redirect($this->url_base . 'settings');
                exit;
            }
        }

        ee()->view->cp_page_title = ee()->lang->line('settings');

        $vars = array();
        $vars['settings'] = $this->settings;
        $vars['query_base'] = $this->query_base;
        $vars['available_themes'] = ee('ee_debug_toolbar:ToolbarService')->getThemes();
        $vars['toolbar_positions'] = ee('ee_debug_toolbar:ToolbarService')->toolbar_positions;
        $vars['settings_disable'] = FALSE;
        if (isset(ee()->config->config['ee_debug_toolbar'])) {
            $vars['settings_disable'] = 'disabled="disabled"';
        }

        return ee()->load->view('settings', $vars, TRUE);
    }
}