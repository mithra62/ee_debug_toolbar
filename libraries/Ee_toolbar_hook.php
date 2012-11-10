<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ee_toolbar_hooks extends CI_Hooks {

    function _run_hook($data) {

        if ( ! is_array($data))
        {
            return FALSE;
        }

        // -----------------------------------
        // Safety - Prevents run-away loops
        // -----------------------------------

        // If the script being called happens to have the same
        // hook call within it a loop can happen

        if ($this->in_progress == TRUE)
        {
            return;
        }

        //We're only interested in the Requirejs_ext hook, everything else,
        //treat as normal
        if($data['class'] != "Ee_debug_toolbar_ext") {
            return parent::_run_hook($data);
        }

        //OK, so here we know we're only processing the RequireJS hook
        // -----------------------------------
        // Set file path
        // -----------------------------------

        if ( ! isset($data['filepath']) OR ! isset($data['filename']))
        {
            return FALSE;
        }

        $filepath = PATH_THIRD.$data['filepath'].'/'.$data['filename'];

        
        if ( ! file_exists($filepath))
        {
            return FALSE;
        }

        // -----------------------------------
        // Set class/function name
        // -----------------------------------

        $class		= FALSE;
        $function	= FALSE;
        $params		= '';

        if (isset($data['class']) AND $data['class'] != '')
        {
            $class = $data['class'];
        }

        if (isset($data['function']))
        {
            $function = $data['function'];
        }

        if (isset($data['params']))
        {
            $params = $data['params'];
        }

        if ($class === FALSE AND $function === FALSE)
        {
            return FALSE;
        }

        // -----------------------------------
        // Set the in_progress flag
        // -----------------------------------

        $this->in_progress = TRUE;

        // -----------------------------------
        // Call the requested class and/or function
        // -----------------------------------

        if ($class !== FALSE)
        {
            if ( ! class_exists($class))
            {
                require($filepath);
            }

            $HOOK = new $class;
            $HOOK->$function($params);
        }
        else
        {
            if ( ! function_exists($function))
            {
                require($filepath);
            }

            $function($params);
        }

        $this->in_progress = FALSE;
        return TRUE;
    }
}