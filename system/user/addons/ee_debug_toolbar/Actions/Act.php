<?php

namespace DebugToolbar\Actions;

use DebugToolbar\Toolbar\GarbageCollection;

class Act extends AbstractAction
{
    /**
     * @return void
     */
    public function process()
    {
        if (!ee('ee_debug_toolbar:ToolbarService')->canViewToolbar()) {
            return;
        }

        $class = ee()->input->get_post('class');
        $method = ee()->input->get_post('method');

        //clean up the file so we know what package we're to include
        $package = strtolower(str_replace(['_ext'], '', $class));

        $errors = true; //let's just assume the worst to keep us honest
        $file_path = PATH_THIRD . $package . '/ext.' . $package . '.php';

        if (file_exists($file_path)) {
            if (!class_exists($class)) {
                include $file_path;
            }

            if (class_exists($class)) {
                $obj = new $class;
                if (is_callable([$obj, $method])) {
                    //now let's make sure the passed method is allowed for use as an ACT
                    if (!empty($obj->eedt_act) && is_array($obj->eedt_act) && in_array($method, $obj->eedt_act)) {
                        $errors = false;
                        $obj->$method(); //paranoid but at least shit won't break.
                    }
                }
            }
        }

        if ($errors) {
            echo 'Ya dun goofed...';
            exit;
        }
    }
}
