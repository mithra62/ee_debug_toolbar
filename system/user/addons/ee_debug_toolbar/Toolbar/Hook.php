<?php

namespace Mithra62\DebugToolbar\Toolbar;

class Hook
{
    public bool $enabled = false;
    public array $hooks = [];
    public $in_progress = FALSE;

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        log_message('debug', "Hooks Class Initialized");
    }

    // --------------------------------------------------------------------

    /**
     * Call Hook
     *
     * Calls a particular hook
     *
     * @access    private
     * @param string    the hook name
     * @return    mixed
     */
    function _call_hook($which = '')
    {
        if (!$this->enabled or !isset($this->hooks[$which])) {
            return FALSE;
        }

        if (isset($this->hooks[$which][0]) and is_array($this->hooks[$which][0])) {
            foreach ($this->hooks[$which] as $val) {
                $this->_run_hook($val);
            }
        } else {
            $this->_run_hook($this->hooks[$which]);
        }

        return TRUE;
    }

    // --------------------------------------------------------------------

    function _run_hook($data)
    {

        if (!is_array($data)) {
            return false;
        }

        // -----------------------------------
        // Safety - Prevents run-away loops
        // -----------------------------------

        // If the script being called happens to have the same
        // hook call within it a loop can happen

        if ($this->in_progress == true) {
            return;
        }

        //We're only interested in the Requirejs_ext hook, everything else,
        //treat as normal
        /*
        if($data['class'] != "Ee_debug_toolbar_ext") {
            return parent::_run_hook($data);
        }
        */

        //OK, so here we know we're only processing the RequireJS hook
        // -----------------------------------
        // Set file path
        // -----------------------------------

        if (!isset($data['filepath']) or !isset($data['filename'])) {
            return false;
        }

        $filepath = PATH_THIRD . $data['filepath'] . '/' . $data['filename'];


        if (!file_exists($filepath)) {
            return false;
        }

        // -----------------------------------
        // Set class/function name
        // -----------------------------------

        $class = false;
        $function = false;
        $params = '';

        if (isset($data['class']) and $data['class'] != '') {
            $class = $data['class'];
        }

        if (isset($data['function'])) {
            $function = $data['function'];
        }

        if (isset($data['params'])) {
            $params = $data['params'];
        }

        if ($class === false and $function === false) {
            return false;
        }

        // -----------------------------------
        // Set the in_progress flag
        // -----------------------------------

        $this->in_progress = true;

        // -----------------------------------
        // Call the requested class and/or function
        // -----------------------------------

        if ($class !== false) {
            if (!class_exists($class)) {
                require($filepath);
            }

            $HOOK = new $class;
            $HOOK->$function($params);
        } else {
            if (!function_exists($function)) {
                require($filepath);
            }

            $function($params);
        }

        $this->in_progress = false;

        return true;
    }
}