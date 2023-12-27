<?php

namespace Mithra62\DebugToolbar\Extensions;

use Mithra62\DebugToolbar\Toolbar\Hook;

class SessionsEnd extends AbstractHook
{
    public function process($session)
    {
        $session = (ee()->extensions->last_call != '' ? ee()->extensions->last_call : $session);

        if (ee()->config->config['show_profiler'] != 'y' || $session->userdata('role_id') != '1') {
            return $session;
        }

        //we don't want to compile Toolbar data on certain requests
        $ignore_controllers = array('javascript', 'css', 'content_files_modal');
        if (in_array(ee()->input->get("C"), $ignore_controllers)) {
            return $session;
        }

        //override to disable the toolbar from even starting
        if (ee()->input->get('disable_toolbar') == 'yes') {
            return $session;
        }

        global $EXT;

        //We overwrite the CI_Hooks class with our own since the CI_Hooks class will always load
        //hooks class files relative to APPPATH, when what we really need is to load RequireJS hook from the
        //third_party folder, which we KNOW can always be found with PATH_THIRD. Hence we extend the class and
        //simply redefine the _run_hook method to load relative to PATH_THIRD. Simples.
        $EET_EXT = new Hook();

        //Capture existing hooks just in case (although this is EE - it's unlikely)
        $EET_EXT->hooks = isset($EXT->hooks) ? $EXT->hooks : [];

        //Enable CI Hooks
        $EET_EXT->enabled = true;

        //Create the post_controller hook array if needed
        if (!isset($EET_EXT->hooks['post_controller'])) {
            $EET_EXT->hooks['post_controller'] = array();
        }

        //Add our hook
        $EET_EXT->hooks['display_override'][] = array(
            'class' => __CLASS__,
            'function' => 'modify_output',
            'filename' => basename(__FILE__),
            'filepath' => "ee_debug_toolbar",
            'params' => array()
        );

        //Overwrite the global CI_Hooks instance with our modified version
        $EXT = $EET_EXT;

        return $session;
    }
}
