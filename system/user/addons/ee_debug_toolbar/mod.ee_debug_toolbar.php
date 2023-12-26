<?php

use Mithra62\DebugToolbar\Toolbar\GarbageCollection;

class Ee_debug_toolbar
{
    /**
     * The data to return from the module
     * @var stirng
     */
    public $return_data = '';

    public function __construct()
    {
        // Make a local reference to the ExpressionEngine super object
        $path = dirname(realpath(__FILE__));
        include $path . '/config.php';
        $this->class = $config['class_name'];
        $this->version = $config['version'];

        ee()->lang->loadfile('ee_debug_toolbar');
        ee()->load->add_package_path(PATH_THIRD . 'ee_debug_toolbar/');

        //run the garbage collection against the cache
        $gc = new GarbageCollection;
        $gc->run();
    }

    public function act()
    {
        $class = ee()->input->get_post('class');
        $method = ee()->input->get_post('method');

        //clean up the file so we know what package we're to include
        $package = strtolower(str_replace(array('_ext'), '', $class));

        $errors = TRUE; //let's just assume the worst to keep us honest
        $file_path = PATH_THIRD . $package . '/ext.' . $package . '.php';
        if (file_exists($file_path)) {
            if (!class_exists($class)) {
                include $file_path;
            }

            if (class_exists($class)) {
                $this->$class = new $class;
                if (is_callable(array($this->$class, $method))) {
                    //now let's make sure the passed method is allowed for use as an ACT
                    if (!empty($this->$class->eedt_act) && is_array($this->$class->eedt_act) && in_array($method, $this->$class->eedt_act)) {
                        $errors = FALSE;
                        $this->$class->$method(); //paranoid but at least shit won't break.
                    }
                }
            }
        }

        if ($errors) {
            //what do we do with calls that aren't good?
            echo 'Ya dun goofed...';
            exit;
        }
    }

    public function tag_test()
    {
        $data = array();
        if (count($data) == '0') {
            return ee()->TMPL->no_results();
        }

        $output = $this->prep_output($data);
        return $output;
    }
}