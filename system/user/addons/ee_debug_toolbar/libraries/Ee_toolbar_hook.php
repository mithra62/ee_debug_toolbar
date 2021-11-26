<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

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
 * EE Debug Toolbar - Hook Library
 *
 * Overrides the default CI hook library so the add-on can be used in custom add_on folders (third_party)
 * STOLEN FROM CHRIS IMRIE WITH PERMISSION
 *
 * @package        mithra62:Ee_toolbar_hook
 * @author         Eric Lamb
 * @filesource     ./system/expressionengine/third_party/ee_debug_toolbar/libraries/Ee_toolbar_hook.php
 */
class Ee_toolbar_hook
{
    public $enabled = FALSE;
    public $hooks = array();
    public $in_progress	= FALSE;

    /**
     * Constructor
     *
     */
    function __construct()
    {
        $this->_initialize();
        log_message('debug', "Hooks Class Initialized");
    }

    // --------------------------------------------------------------------

    /**
     * Initialize the Hooks Preferences
     *
     * @access	private
     * @return	void
     */
    function _initialize()
    {
        $CFG = load_class('Config', 'core');

        // If hooks are not enabled in the config file
        // there is nothing else to do

        if ($CFG->item('enable_hooks') == FALSE)
        {
            return;
        }

        // Grab the "hooks" definition file.
        // If there are no hooks, we're done.

        @include(APPPATH.'config/hooks.php');

        if ( ! isset($hook) OR ! is_array($hook))
        {
            return;
        }

        $this->hooks =& $hook;
        $this->enabled = TRUE;
    }

    // --------------------------------------------------------------------

    /**
     * Call Hook
     *
     * Calls a particular hook
     *
     * @access	private
     * @param	string	the hook name
     * @return	mixed
     */
    function _call_hook($which = '')
    {
        if ( ! $this->enabled OR ! isset($this->hooks[$which]))
        {
            return FALSE;
        }

        if (isset($this->hooks[$which][0]) AND is_array($this->hooks[$which][0]))
        {
            foreach ($this->hooks[$which] as $val)
            {
                $this->_run_hook($val);
            }
        }
        else
        {
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

		if (!isset($data['filepath']) OR !isset($data['filename'])) {
			return false;
		}

		$filepath = PATH_THIRD . $data['filepath'] . '/' . $data['filename'];


		if (!file_exists($filepath)) {
			return false;
		}

		// -----------------------------------
		// Set class/function name
		// -----------------------------------

		$class    = false;
		$function = false;
		$params   = '';

		if (isset($data['class']) AND $data['class'] != '') {
			$class = $data['class'];
		}

		if (isset($data['function'])) {
			$function = $data['function'];
		}

		if (isset($data['params'])) {
			$params = $data['params'];
		}

		if ($class === false AND $function === false) {
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