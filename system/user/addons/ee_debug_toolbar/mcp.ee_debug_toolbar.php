<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * mithra62 - EE Debug Toolbar
 *
 * @package        mithra62:EE_debug_toolbar
 * @author         Eric Lamb
 * @copyright      Copyright (c) 2013, mithra62, Eric Lamb.
 * @link           http://mithra62.com/
 * @updated        1.0
 * @filesource     ./system/expressionengine/third_party/ee_debug_toolbar/
 */

/**
 * EE Debug Toolbar - CP Class
 *
 * Control Panel class
 *
 * @package        mithra62:EE_debug_toolbar
 * @author         Eric Lamb
 * @filesource     ./system/expressionengine/third_party/ee_debug_toolbar/mcp.ee_debug_toolbar.php
 */
class Ee_debug_toolbar_mcp
{
    /**
     * The URL to the module
     * @var string
     */
    public $url_base = '';

    /**
     * The name of the module; used for links and whatnots
     * @var string
     */
    private $mod_name = '';

    /**
     * The breadcrumb override
     * @var array
     */
    protected static $_breadcrumbs = array();

    public function __construct()
    {
        $path = dirname(realpath(__FILE__));
        include $path . '/config.php';
        $this->class = $config['class_name'];
        $this->version = $config['version'];
        $this->mod_name = $config['mod_url_name'];

        $this->query_base = 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=' . $this->mod_name . AMP . 'method=';
        $this->url_base = BASE . AMP . $this->query_base;

        ee()->cp->set_breadcrumb(BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=' . $this->mod_name, ee()->lang->line('ee_debug_toolbar_module_name'));

        $this->add_breadcrumb(BASE . AMP . 'C=addons', lang('add_ons'));
        $this->add_breadcrumb(BASE . AMP . 'C=addons_extensions', lang('extensions'));
        $this->add_breadcrumb($this->url_base . 'index', lang('ee_debug_toolbar_module_name'));
    }

    private function add_breadcrumb($link, $title)
    {
        self::$_breadcrumbs[$link] = $title;
        ee()->load->vars(array('cp_breadcrumbs' => self::$_breadcrumbs));
    }

    public function index()
    {
        echo 'fdsa';
        exit;
        //ee()->functions->redirect($this->url_base . 'settings');
    }

    public function settings()
    {
        ee()->load->library('table');
        ee()->load->library('toolbar');
        $this->settings = ee()->toolbar->get_settings();

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
        $vars['available_themes'] = ee()->toolbar->get_themes();
        $vars['toolbar_positions'] = ee()->toolbar->toolbar_positions;
        $vars['settings_disable'] = FALSE;
        if (isset(ee()->config->config['ee_debug_toolbar'])) {
            $vars['settings_disable'] = 'disabled="disabled"';
        }

        return ee()->load->view('settings', $vars, TRUE);
    }
}