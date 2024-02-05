<?php

namespace DebugToolbar\Extensions;

use DebugToolbar\Panels\Model;

class ResponseSendOutput extends AbstractHook
{
    public function process()
    {
        //Attempt to patch the weird unfinished Active record chain (issue #18)
        ee()->db->limit(1)->get("channel_titles");

        //we have to check if the profiler and debugging is enabled again so other add-ons and templates can disable things if they want to
        //see Issue #48 for details (https://github.com/mithra62/ee_debug_toolbar/issues/48)
        if (ee()->config->config['show_profiler'] != 'y' || ee()->output->enable_profiler != '1') {
            //return;
        }

        //override to disable the toolbar from even starting
        if (ee()->input->get('disable_toolbar') == 'yes' ||
            ee()->input->get('C') == 'javascript' ||
            (ee()->input->get('ACT') && ee()->input->get('frontedit') == 'on') ||
            (ee()->input->get('ACT') && ee()->input->get('prolet')) ||
            (strpos(ee()->input->server('REQUEST_URI'), 'themes/ee/pro/js') !== false) || //Pro Edit
            ee()->input->get('modal_form') == 'y' ||
            (ee()->input->get('ui') && ee()->input->get('plugin') == 'markitup') ||
            (ee()->input->get('D') == 'cp' && ee()->input->get('C') == 'jumps')
        ) {
            return;
        }

        if (!ee('ee_debug_toolbar:ToolbarService')->canViewToolbar()) {
            return;
        }

        $html = ee()->output->final_output;

        //If its an AJAX request (eg: EE JS Combo loader or jQuery library load) then call it a day...
        $ignore_tmpl_types = ['js', 'css', 'feed'];
        if (AJAX_REQUEST ||
            (property_exists(ee(), "TMPL") && in_array(ee()->TMPL->template_type, $ignore_tmpl_types)) ||
            (isset(ee()->TMPL->template_type) && in_array(ee()->TMPL->template_type, $ignore_tmpl_types))
        ) {
            return;
        }

        //starting a benchmark to make sure we're not a problem
        ee()->benchmark->mark('ee_debug_benchmark_start');

        $this->settings = $this->toolbar->getSettings();

        //on 404 errors this can cause the data to get munged
        //to get around this, we only want to run the toolbar on certain pages
        ///see $this->settings['profile_exts'] for details
        //NOTE: bookmarklets create an error for parse_url() so if it's a request for that we know we're good anyway so we disable check
        if (ee()->input->get("tb_url") == '') {
            $url = parse_url($_SERVER['REQUEST_URI']);
            if (!empty($url['path'])) {
                $parts = explode(".", $url['path'], 2);
                if (!empty($parts['1'])) {
                    if (in_array($parts['1'], $this->settings['profile_exts'])) {
                        return;
                    }
                }
            }
        }

        //Toolbar UI Vars
        $vars = [];
        $vars['mysql_query_cache'] = $this->toolbar->verifyMysqlQueryCache();
        $vars['elapsed_time'] = ee()->benchmark->elapsed_time('total_execution_time_start', 'total_execution_time_end');
        $vars['config_data'] = ee()->config->config;
        $vars['session_data'] = ee()->session->all_userdata();
        $vars['query_data'] = $this->toolbar->setupQueries();
        $vars['memory_usage'] = $this->toolbar->filesizeFormat(memory_get_peak_usage());
        $vars['memory_usage_raw'] = memory_get_peak_usage();
        $vars['template_debugging_enabled'] = isset(ee()->TMPL->log) && is_array(ee()->TMPL->log) && count(ee()->TMPL->log) > 0;
        $vars['template_debugging'] = ($vars['template_debugging_enabled'] ? $this->toolbar->formatTmplLog(ee()->TMPL->log) : []);
        $vars['template_debugging_chart_json'] = ($vars['template_debugging_enabled'] ? $this->toolbar->formatTmplChartJson($vars['template_debugging']) : '');
        $vars['included_file_data'] = $this->toolbar->setupFiles(get_included_files());
        $vars['cookie_data'] = $this->toolbar->setupCookies();

        $vars['ext_version'] = $this->version;
        $this->settings = $this->toolbar->getSettings();
        $vars['theme_img_url'] = $this->toolbar->createThemeUrl($this->settings['theme'], 'images');
        $vars['theme_js_url'] = $this->toolbar->createThemeUrl($this->settings['theme'], 'js');
        $vars['theme_css_url'] = $this->toolbar->createThemeUrl($this->settings['theme'], 'css');
        $vars['extra_html'] = ''; //used by extension to add extra script/css files
        $vars['eedt_theme_path'] = (defined('PATH_THIRD_THEMES') ? PATH_THIRD_THEMES : rtrim(ee()->config->config['theme_folder_path'], '/third_party/') . '/') . 'ee_debug_toolbar/themes/' . $this->settings['theme'];
        $vars['master_view_script'] = "toolbar";
        $vars['panels'] = [];
        $vars['toolbar_position'] = $this->determineToolbarPositionClass();
        $vars['js'] = [$vars['theme_js_url'] . "eedt.js"];
        $vars['css'] = [$vars['theme_css_url'] . "ee_debug_toolbar.css"];
        $vars['benchmark_data'] = []; //we have to fake this for now
        $vars['settings'] = $this->settings;
        $vars['template_groups'] = $this->toolbar->getTemplateGroups();
        //$vars['query_count'] = ee()->db->query_count;

        //Load variables so that they are present in all view partials
        ee()->load->vars($vars);

        //Load Internal Panels & load view model data
        $panels = $this->loadPanels();
        $panel_data = [];
        foreach ($panels as $panel) {
            $p = $panel->addPanel(new Model());
            $panel_data[$p->getName()] = $p;
        }

        //Load third party panels and custom mods
        //yes, you can technically create panels in mod_panel but using
        //add_panel will help future proof things
        if (ee()->extensions->active_hook('ee_debug_toolbar_add_panel') === true) {
            $panel_data = ee()->extensions->call('ee_debug_toolbar_add_panel', $panel_data, $vars);
        }

        //do... stuff... to panels... eventually...

        //apply custom modifications to toolbar
        //again, yes, you could create panels using mod_panel but you probably shouldn't ;)
        if (ee()->extensions->active_hook('ee_debug_toolbar_mod_panel') === true) {
            $panel_data = ee()->extensions->call('ee_debug_toolbar_mod_panel', $panel_data, $vars);
        }

        //have to verify the panels are good after letting the users have a go...
        foreach ($panel_data as $key => $panel) {
            if (!($panel instanceof Model)) {
                unset($panel_data[$key]);
                continue;
            }

            //If any panels have specified JS & CSS to be inserted on page load, collect them here
            $vars['css'] = array_merge($vars['css'], $panel->getPageLoadCss());
            $vars['js'] = array_merge($vars['js'], $panel->getPageLoadJs());
        }

        $vars['panels'] = $panel_data;
        $vars['js_config'] = $this->toolbar->jsConfig($vars);

        //apply any customizations to the global view data
        if (ee()->extensions->active_hook('ee_debug_toolbar_mod_view') === true) {
            $vars = ee()->extensions->call('ee_debug_toolbar_mod_view', $vars);
        }

        //we have to "redo" the benchmark panel so we have all the internal benchmarks
        //COULD WREAK HAVOC ON BENCHMARK OVERRIDES!!!
        ee()->benchmark->mark('ee_debug_benchmark_end');
        $vars['benchmark_data'] = $this->toolbar->setupBenchmarks();
        if (!empty($vars['panels']['time'])) {
            $vars['panels']['time']->setPanelContents(ee()->load->view("partials/time", $vars, true));
        }

        //check total time
        if ($vars['elapsed_time'] > $this->settings['max_exec_time']) {
            $vars['panels']['time']->setPanelCssClass('flash');
        }

        //Break up the panels into the various injection points
        $vars['panels_before_toolbar'] = [];
        $vars['panels_in_toolbar'] = [];
        $vars['panels_after_toolbar'] = [];

        foreach ($vars['panels'] as $panel) {
            switch ($panel->getInjectionPoint()) {
                case Model::PANEL_BEFORE_TOOLBAR:
                    $vars['panels_before_toolbar'][] = $panel;
                    break;
                case Model::PANEL_IN_TOOLBAR:
                    $vars['panels_in_toolbar'][] = $panel;
                    break;
                case Model::PANEL_AFTER_TOOLBAR:
                    $vars['panels_after_toolbar'][] = $panel;
                    break;
            }
        }

        unset($vars['panels']);

        //setup the XML storage data for use by the panels on open
        $this->toolbar->cachePanels(ee('ee_debug_toolbar:XmlService'), $vars['panels_in_toolbar'], $this->cache_dir);

        //Render toolbar
        $toolbar_html = ee()->load->view($vars['master_view_script'], $vars, true);

        //Allow modification of final toolbar HTML output
        if (ee()->extensions->active_hook('ee_debug_toolbar_modify_output') === true) {
            $toolbar_html = ee()->extensions->call('ee_debug_toolbar_modify_output', $toolbar_html);
        }

        //Rare, but the closing body tag may not exist. So if it doesnt, append the template instead
        //of inserting. We may be able to get away with simply always appending, but this seems cleaner
        //even if more expensive.
        if (strpos($html, "</body>") === false) {
            $html .= $toolbar_html;
        } else {
            $html = str_replace('</body>', $toolbar_html . '</body>', $html);
        }

        //Get CI to do its usual thing and build the final output, but we'll switch off the debugging
        //since we have already added the debug data to the body output. Doing it this way means
        //we should retain 100% compatibility (I'm looking at you Stash...)
        ee()->output->final_output = $html;
        if (isset(ee()->TMPL)) {
            ee()->TMPL->debugging = false;
            ee()->TMPL->log = false;
        }

        ee()->output->enable_profiler = false;
    }

    /**
     * Loads Native EEDT Panel Extensions
     * @return array
     */
    private function loadPanels(): array
    {
        $instances = [];

        ee()->load->helper("file");
        $files = get_filenames(PATH_THIRD . "ee_debug_toolbar/Panels/");


        //setup the array in the order we want the panels to appear
        $sorted_files = [];
        foreach ($this->panel_order as $panel) {

            $name = $panel . '.php';
            if (in_array($name, $files)) {
                $sorted_files[] = $panel;
            }
        }

        //each panel is an object so set them up
        foreach ($sorted_files as $file) {

            $class = '\\DebugToolbar\Panels\\' . str_replace(".php", "", $file);
            if (class_exists($class)) {
                $instances[$class] = new $class();
            }
        }

        return $instances;
    }

    /**
     * Determine the toolbar position classes to be added to the toolbar root node
     * @return string
     */
    private function determineToolbarPositionClass(): string
    {
        if (!array_key_exists("toolbar_position", $this->settings)) {
            $this->settings['toolbar_position'] = 0;
        }

        switch ($this->settings['toolbar_position']) {
            case 1:
                $position = "top left";
                break;
            case 2:
                $position = "bottom right";
                break;
            case 3:
                $position = "top right";
                break;
            default:
                $position = "bottom left";
                break;
        }

        return $position;
    }
}
