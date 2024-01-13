<?php

namespace DebugToolbar\Services;

class ToolbarService
{
    /**
     * @var array
     */
    protected array $template_groups = [];

    /**
     * The theme to use if no other theme is set
     * @var string
     */
    public string $default_theme = "default";

    /**
     * The available positions for the toolbar to live
     * @var array
     */
    public array $toolbar_positions = [
        'bottom-left',
        'top-left',
        'bottom-right',
        'top-right',
    ];

    /**
     * Wrapper to setup and return the toolbar settings
     */
    public function getSettings()
    {
        if (!isset(ee()->session->cache['ee_debug_toolbar']['settings'])) {
            if (ee()->extensions->active_hook('ee_debug_toolbar_init_settings') === true) {
                $defaults = ee('ee_debug_toolbar:SettingsService')->getDefaults();
                $defaults = ee()->extensions->call('ee_debug_toolbar_init_settings', $defaults);
                ee('ee_debug_toolbar:SettingsService')->setDefaults($defaults);
            }
            ee()->session->cache['ee_debug_toolbar']['settings'] = ee('ee_debug_toolbar:SettingsService')->getSettings();
        }

        return ee()->session->cache['ee_debug_toolbar']['settings'];
    }

    /**
     * Takes the included files and breaks up into mutli arrays for use in the debugger
     * @param array $files
     * @return array
     */
    public function setupFiles(array $files): array
    {
        sort($files);

        $path_third = realpath(ee('ee_debug_toolbar:OutputService')->thirdPartyPath());
        $path_ee = realpath(SYSPATH);
        $path_first_modules = realpath(PATH_MOD);
        $bootstrap_file = FCPATH . SELF;
        $return = [
            'third_party_addon' => [],
            'first_party_modules' => [],
            'bootstrap_file' => [],
            'expressionengine_core' => [],
            'composer' => [],
            'other_files' => [],
        ];

        foreach ($files as $file) {
            $output = str_replace($path_ee, '#system', $file);
            if (strpos($file, 'autoload.php') !== false) {
                $return['composer'][] = $output;
            }

            if (strpos($file, $path_third) === 0) {
                $return['third_party_addon'][] = $output;
                continue;
            }

            if (strpos($file, $path_first_modules) === 0) {
                $return['first_party_modules'][] = $output;
                continue;
            }

            if (strpos($file, $bootstrap_file) === 0) {
                $return['bootstrap_file'] = $output;
                continue;
            }

            if (strpos($file, $path_ee) === 0) {
                $return['expressionengine_core'][] = $output;
                continue;
            }

            $return['other_files'][] = $output;
        }

        return $return;
    }

    public function setupCookies(): array
    {
        $providers = ee('App')->getProviders();
        $consents = $cookies = [];

        //echo ee('ee:CookieRegistry')->loadCookiesSettings();
        foreach($_COOKIE AS $key => $value) {
            if(str_starts_with($key, 'exp_')) {
                $old_key = $key;
                $key = substr($key, 4, strlen($key));
                if(ee('ee:CookieRegistry')->isRegistered($key)) {
                    $type = 'Necessary';
                    if(ee('ee:CookieRegistry')->isTargeting($key)) {
                        $type = 'Targeting';
                    } else if(ee('ee:CookieRegistry')->isPerformance($key)) {
                        $type = 'Performance';
                    } else if(ee('ee:CookieRegistry')->isFunctionality($key)) {
                        $type = 'Functionality';
                    }

                    $consents[$key] = array_merge([
                        'key' => $old_key,
                        'value' => $value,
                        'type' => $type,
                    ], ee('ee:CookieRegistry')->getCookieSettings($key));

                } else {
                    $cookies[$old_key] = $value;
                }

            } else {
                $cookies[$key] = $value;
            }
        }

        return [
            'registered' => $consents,
            'unregistered' => $cookies
        ];
    }

    /**
     * Wrapper to setup the Database panel SQL queries
     * @return array
     */
    public function setupQueries(): array
    {
        $dbs = [];

        // Let's determine which databases are currently connected to
        foreach (get_object_vars(ee()) as $EE_object) {
            if (is_object($EE_object) && is_subclass_of(get_class($EE_object), 'CI_DB')) {
                $dbs[] = $EE_object;
            }
        }

        $output = [];
        if (count($dbs) == 0) {
            return $output;
        }
        // Load the text helper so we can highlight the SQL
        ee()->load->helper('text');

        $count = 0;
        $total_time = 0;
        foreach ($dbs as $db) {
            $count++;

            if (count($db->queries) != 0) {
                foreach ($db->queries as $key => $val) {
                    $total_time = $total_time + $db->query_times[$key];
                    $time = number_format($db->query_times[$key], 4);
                    $output['queries'][] = ['query' => highlight_code($val, ENT_QUOTES), 'time' => $time];
                }
            }

        }

        $output['total_time'] = number_format($total_time, 4);

        return $output;
    }

    /**
     * Wrapper to setup the benchmark data
     * @return array
     */
    public function setupBenchmarks()
    {
        $profile = [];
        foreach (ee()->benchmark->marker as $key => $val) {
            // We match the "end" marker so that the list ends
            // up in the order that it was defined
            if (preg_match("/(.+?)_end/i", $key, $match)) {
                if (isset(ee()->benchmark->marker[$match[1] . '_end']) and isset(ee()->benchmark->marker[$match[1] . '_start'])) {
                    $profile[$match[1]] = ee()->benchmark->elapsed_time($match[1] . '_start', $key);
                }
            }
        }

        return $profile;
    }

    /**
     * Breaks up the template log data to create the chart
     *
     * @param array $log
     * @return array
     */
    public function formatTmplLog(array $log)
    {
        $return = [];
        foreach ($log as $item) {
            $return[] = [
                'time' => $item['time'],
                'memory' => (float)$item['memory'],
                'desc' => utf8_encode($item['message']), //a little sanity for UTF-8
            ];
        }

        return $return;
    }

    /**
     * Returns a JSON string that can be used by the Template Chart JS
     *
     * @param $log array
     * @return string
     */
    public function formatTmplChartJson(array $data): string
    {
        foreach($data AS $key => $value) {
            $data[$key]['desc'] = preg_replace("/&#?[a-z0-9]{2,8};/i",'', $data[$key]['desc']);
            $data[$key]['memory_display'] = ee('ee_debug_toolbar:ToolbarService')->filesizeFormat($data[$key]['memory']);
            $data[$key]['time'] = number_format($data[$key]['time'], 4);
        }

        return json_encode($data);
    }

    /**
     * Format a number of bytes into a human readable format.
     * Optionally choose the output format and/or force a particular unit
     *
     * @param int $bytes The number of bytes to format. Must be positive
     * @param string $format Optional. The output format for the string
     * @param string $force Optional. Force a certain unit. B|KB|MB|GB|TB
     * @return  string              The formatted file size
     */
    public function filesizeFormat($val, $digits = 3, $mode = "SI", $bB = "B")
    { //$mode == "SI"|"IEC", $bB == "b"|"B"
        $si = ["", "k", "M", "G", "T", "P", "E", "Z", "Y"];
        $iec = ["", "Ki", "Mi", "Gi", "Ti", "Pi", "Ei", "Zi", "Yi"];
        switch (strtoupper($mode)) {
            case "SI" :
                $factor = 1000;
                $symbols = $si;
                break;
            case "IEC" :
                $factor = 1024;
                $symbols = $iec;
                break;
            default :
                $factor = 1000;
                $symbols = $si;
                break;
        }
        switch ($bB) {
            case "b" :
                $val *= 8;
                break;
            default :
                $bB = "B";
                break;
        }
        for ($i = 0; $i < count($symbols) - 1 && $val >= $factor; $i++) {
            $val /= $factor;
        }
        $p = strpos($val, ".");
        if ($p !== false && $p > $digits) {
            $val = round($val);
        } else if ($p !== false) {
            $val = round($val, $digits - $p);
        }

        return round($val, $digits) . " " . $symbols[$i] . $bB;
    }

    /**
     * Checks the system for the available themes and sets up as $key => $value array
     * @return array
     */
    public function getThemes()
    {
        $path = ee('ee_debug_toolbar:OutputService')->themePath() . '/ee_debug_toolbar/themes/';
        $d = dir($path);
        $themes = [];
        $bad = ['.', '..'];
        while (false !== ($entry = $d->read())) {
            if (is_dir($path . $entry) && !in_array($entry, $bad)) {
                $name = ucwords(str_replace('_', ' ', $entry));
                $themes[$entry] = $name;
            }
        }
        $d->close();
        return $themes;
    }

    /**
     * @return string[]
     */
    public function getDisplayErrorCodes(): array
    {
        return [
            E_ERROR => 'E_ERROR',
            E_WARNING => 'E_WARNING',
            E_PARSE => 'E_PARSE',
            E_NOTICE => 'E_NOTICE',
            E_CORE_ERROR => 'E_CORE_ERROR',
            E_CORE_WARNING => 'E_CORE_WARNING',
            E_COMPILE_ERROR => 'E_COMPILE_ERROR',
            E_COMPILE_WARNING => 'E_COMPILE_WARNING',
            E_USER_ERROR => 'E_USER_ERROR',
            E_USER_WARNING => 'E_USER_WARNING',
            E_USER_NOTICE => 'E_USER_NOTICE',
            E_STRICT => 'E_STRICT',
            E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
            E_DEPRECATED => 'E_DEPRECATED',
            E_USER_DEPRECATED => 'E_USER_DEPRECATED',
        ];
    }

    /**
     * Create Theme Asset URLs
     *
     * @param string $theme
     * @return string
     */
    public function createThemeUrl($theme, $sub_dir = '')
    {
        $path = ee('ee_debug_toolbar:OutputService')->themePath();
        $url = ee('ee_debug_toolbar:OutputService')->themeUrl();
        if (is_dir($path . "ee_debug_toolbar/themes/" . $theme . "/$sub_dir/")) {
            return $url . "ee_debug_toolbar/themes/" . $theme . "/$sub_dir/";
        }

        return $url . "ee_debug_toolbar/themes/" . $this->default_theme . "/$sub_dir/";
    }

    /**
     * Returns the ACT for the given params
     * @param string $class
     * @param string $method
     */
    public function fetchActionId($method, $class)
    {
        ee()->load->dbforge();
        ee()->db->select('action_id');
        $query = ee()->db->get_where('actions', ['class' => $class, 'method' => $method]);
        return $query->row('action_id');
    }

    /**
     * Returns the action URL for the given params
     * @param string $method
     * @param string $class
     */
    public function getActionUrl($method, $class = 'Ee_debug_toolbar')
    {
        $url = site_url();
        return $url . '?ACT=' . $this->fetchActionId($method, $class);
    }

    /**
     * Creates the internal ACT URL for use by extensions
     * @param string $act_method
     * @param string $act_class
     * @return string
     */
    public function createActUrl($act_method, $act_class = 'Ee_debug_toolbar_ext')
    {
        return $url = $this->getActionUrl('act') . AMP . 'class=' . $act_class . AMP . 'method=' . $act_method;
    }

    /**
     * Takes the panel data and writes them to $path
     * @param array $panels
     * @param string $path
     */
    public function cachePanels($panels, $path)
    {
        ee()->load->library('xml_writer');
        ee()->xml_writer->setRootName('EEDT');
        ee()->xml_writer->initiate();
        ee()->xml_writer->startBranch('panels');
        foreach ($panels as $panel) {
            ee()->xml_writer->startBranch($panel->getName() . '_panel');
            ee()->xml_writer->addNode('name', $panel->getName(), [], true);
            ee()->xml_writer->addNode('data_target', $panel->getTarget(), [], true);
            ee()->xml_writer->addNode('button_icon', $panel->getButtonIcon(), [], true);
            ee()->xml_writer->addNode('button_icon_alt_text', $panel->getButtonIconAltText(), [], true);
            ee()->xml_writer->addNode('button_label', $panel->getButtonLabel(), [], true);
            ee()->xml_writer->addNode('output', base64_encode($panel->getPanelContents()), [], true);
            ee()->xml_writer->endBranch();
        }

        ee()->xml_writer->endBranch();
        $xml = ee()->xml_writer->getXml(false);

        $filename = $path . $this->makeCacheFilename();

        $string = utf8_encode($xml);
        $gz = gzopen($filename . '.gz', 'w9');
        gzwrite($gz, $string);
        gzclose($gz);

        chmod($filename . '.gz', 0777);

        //write_file($filename, utf8_encode($xml));
    }

    /**
     * Creates the Toolbar panel cache filename
     * @return string
     */
    public function makeCacheFilename()
    {
        return '.' . ee()->session->userdata['session_id'] . '.eedt';
    }

    /**
     * Builds the JS Config to be output as JSON
     *
     * @param array $vars
     */
    public function jsConfig($vars = [])
    {
        $config = [];

        $config['template_debugging_enabled'] = $vars['template_debugging_enabled'];

        //Panels
        $config['panels'] = [];
        $config['cp'] = ee()->input->get('D') == 'cp' ? true : false;
        $config['base_css_url'] = $vars['theme_css_url'];
        $config['base_js_url'] = $vars['theme_js_url'];
        $config['panel_ajax_url'] = str_replace("&amp;", "&", $this->getActionUrl('act') . AMP);

        /**
         * @var Model $panel
         */
        foreach ($vars['panels'] as $panel) {
            $config['panels'][] = [
                'name' => $panel->getName(),
                'js' => $panel->getJs(),
                'css' => $panel->getCss(),
                'panel_fetch_url' => $panel->getPanelFetchUrl() ? $panel->getPanelFetchUrl() :
                    str_replace("&amp;", "&", $this->createActUrl("get_panel_data")) . "&panel=" . $panel->getName(),
            ];
        }

        return $config;
    }

    /**
     * Checks to verify the MySQL Query Cache is enabled or not
     * @return string
     */
    public function verifyMysqlQueryCache()
    {
        $data = ee()->db->query("SHOW VARIABLES LIKE 'have_query_cache'")->row_array();
        if (!empty($data['Value']) && $data['Value'] == 'YES') {
            return 'y';
        }
    }

    public function getTemplateGroups(): array
    {
        if(!$this->template_groups) {
            $groups = ee()->db->select()->from('template_groups')->get()->result_array();
            foreach($groups As $group) {
                $this->template_groups[$group['group_id']] = $group['group_name'];
            }
        }

        return $this->template_groups;
    }
}