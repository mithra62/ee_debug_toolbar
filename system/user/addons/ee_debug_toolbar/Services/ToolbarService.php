<?php

namespace Mithra62\DebugToolbar\Services;

class ToolbarService
{
    /**
     * The theme to use if no other theme is set
     * @var string
     */
    public $default_theme = "default";

    /**
     * The available positions for the toolbar to live
     * @var array
     */
    public $toolbar_positions = array(
        'bottom-left',
        'top-left',
        'bottom-right',
        'top-right'
    );

    public function __construct()
    {
        ee()->load->helpers('eedt_output');
    }

    /**
     * Wrapper to setup and return the toolbar settings
     */
    public function get_settings()
    {
        if (!isset(ee()->session->cache['ee_debug_toolbar']['settings'])) {
            ee()->load->model('ee_debug_settings_model', 'debug_settings');
            if (ee()->extensions->active_hook('ee_debug_toolbar_init_settings') === TRUE) {
                $defaults = array();
                $defaults = ee()->extensions->call('ee_debug_toolbar_init_settings', $defaults);
                ee()->debug_settings->set_defaults($defaults);
            }
            ee()->session->cache['ee_debug_toolbar']['settings'] = ee()->debug_settings->get_settings();
        }

        return ee()->session->cache['ee_debug_toolbar']['settings'];
    }

    /**
     * Takes the included files and breaks up into mutli arrays for use in the debugger
     * @param array $files
     * @return Ambigous <multitype:unknown , unknown>
     */
    public function setup_files(array $files)
    {
        sort($files);

        $path_third = realpath(eedt_third_party_path());
        $path_ee = realpath(APPPATH);
        $path_first_modules = realpath(PATH_MOD);
        $bootstrap_file = FCPATH . SELF;
        $return = array();
        foreach ($files as $file) {
            if (strpos($file, $path_third) === 0) {
                $return['third_party_addon'][] = $file;
                continue;
            }

            if (strpos($file, $path_first_modules) === 0) {
                $return['first_party_modules'][] = $file;
                continue;
            }

            if (strpos($file, $bootstrap_file) === 0) {
                $return['bootstrap_file'] = $file;
                continue;
            }

            if (strpos($file, $path_ee) === 0) {
                $return['expressionengine_core'][] = $file;
                continue;
            }

            $return['other_files'][] = $file;

        }

        return $return;
    }

    /**
     * Wrapper to setup the Database panel SQL queries
     * @return multitype:|multitype:string
     */
    public function setup_queries()
    {
        $dbs = array();

        // Let's determine which databases are currently connected to
        foreach (get_object_vars(ee()) as $EE_object) {
            if (is_object($EE_object) && is_subclass_of(get_class($EE_object), 'CI_DB')) {
                $dbs[] = $EE_object;
            }
        }

        $output = array();
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
                    $output['queries'][] = array('query' => highlight_code($val, ENT_QUOTES), 'time' => $time);
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
    public function setup_benchmarks()
    {
        $profile = array();
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
    public function format_tmpl_log(array $log)
    {
        $return = array();
        foreach ($log as $item) {
            $return[] = array(
                'time' => $item['time'],
                'memory' => (float)$item['memory'],
                'desc' => utf8_encode($item['message']) //a little sanity for UTF-8
            );
        }

        return $return;
    }

    /**
     * Returns a JSON string that can be used by the Template Chart JS
     *
     * @param $log array
     * @return string
     */
    public function format_tmpl_chart_json(array $data)
    {
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
    public function filesize_format($val, $digits = 3, $mode = "SI", $bB = "B")
    { //$mode == "SI"|"IEC", $bB == "b"|"B"

        $si = array("", "k", "M", "G", "T", "P", "E", "Z", "Y");
        $iec = array("", "Ki", "Mi", "Gi", "Ti", "Pi", "Ei", "Zi", "Yi");
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
        } elseif ($p !== false) {
            $val = round($val, $digits - $p);
        }

        return round($val, $digits) . " " . $symbols[$i] . $bB;
    }

    /**
     * Checks the system for the available themes and sets up as $key => $value array
     * @return array
     */
    public function get_themes()
    {
        $path = eedt_theme_path() . '/ee_debug_toolbar/themes/';
        $d = dir($path);
        $themes = array();
        $bad = array('.', '..');
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
     * Create Theme Asset URLs
     *
     * @param string $theme
     * @return string
     */
    public function create_theme_url($theme, $sub_dir = '')
    {
        $path = eedt_theme_path();
        $url = eedt_theme_url();
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
    public function fetch_action_id($method, $class)
    {
        ee()->load->dbforge();
        ee()->db->select('action_id');
        $query = ee()->db->get_where('actions', array('class' => $class, 'method' => $method));
        return $query->row('action_id');
    }

    /**
     * Returns the action URL for the given params
     * @param string $method
     * @param string $class
     */
    public function get_action_url($method, $class = 'Ee_debug_toolbar')
    {
        $url = site_url();
        return $url . '?ACT=' . $this->fetch_action_id($method, $class);
    }

    /**
     * Creates the internal ACT URL for use by extensions
     * @param string $act_method
     * @param string $act_class
     * @return string
     */
    public function create_act_url($act_method, $act_class = 'Ee_debug_toolbar_ext')
    {
        return $url = $this->get_action_url('act') . AMP . 'class=' . $act_class . AMP . 'method=' . $act_method;
    }

    /**
     * Takes the panel data and writes them to $path
     * @param array $panels
     * @param string $path
     */
    public function cache_panels($panels, $path)
    {
        ee()->load->library('xml_writer');
        ee()->xml_writer->setRootName('EEDT');
        ee()->xml_writer->initiate();
        ee()->xml_writer->startBranch('panels');
        foreach ($panels as $panel) {
            ee()->xml_writer->startBranch($panel->get_name() . '_panel');
            ee()->xml_writer->addNode('name', $panel->get_name(), array(), TRUE);
            ee()->xml_writer->addNode('data_target', $panel->get_target(), array(), TRUE);
            ee()->xml_writer->addNode('button_icon', $panel->get_button_icon(), array(), TRUE);
            ee()->xml_writer->addNode('button_icon_alt_text', $panel->get_button_icon_alt_text(), array(), TRUE);
            ee()->xml_writer->addNode('button_label', $panel->get_button_label(), array(), TRUE);
            ee()->xml_writer->addNode('output', base64_encode($panel->get_panel_contents()), array(), TRUE);
            ee()->xml_writer->endBranch();
        }

        ee()->xml_writer->endBranch();
        $xml = ee()->xml_writer->getXml(false);

        $filename = $path . $this->make_cache_filename();

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
    public function make_cache_filename()
    {
        return '.' . ee()->session->userdata['session_id'] . '.eedt';
    }

    /**
     * Builds the JS Config to be output as JSON
     *
     * @param array $vars
     */
    public function js_config($vars = array())
    {
        $config = array();

        $config['template_debugging_enabled'] = $vars['template_debugging_enabled'];

        //Panels
        $config['panels'] = array();
        $config['cp'] = ee()->input->get('D') == 'cp' ? true : false;
        $config['base_css_url'] = $vars['theme_css_url'];
        $config['base_js_url'] = $vars['theme_js_url'];
        $config['panel_ajax_url'] = str_replace("&amp;", "&", $this->get_action_url('act') . AMP);

        /**
         * @var Eedt_panel_model $panel
         */
        foreach ($vars['panels'] as $panel) {
            $config['panels'][] = array(
                'name' => $panel->get_name(),
                'js' => $panel->get_js(),
                'css' => $panel->get_css(),
                'panel_fetch_url' => $panel->get_panel_fetch_url() ? $panel->get_panel_fetch_url() :
                    str_replace("&amp;", "&", $this->create_act_url("get_panel_data")) . "&panel=" . $panel->get_name()
            );
        }

        return $config;
    }

    /**
     * Checks to verify the MySQL Query Cache is enabled or not
     * @return string
     */
    public function verify_mysql_query_cache()
    {
        $data = ee()->db->query("SHOW VARIABLES LIKE 'have_query_cache'")->row_array();
        if (!empty($data['Value']) && $data['Value'] == 'YES') {
            return 'y';
        }
    }
}