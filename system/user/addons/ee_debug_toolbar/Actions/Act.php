<?php

namespace DebugToolbar\Actions;

use ExpressionEngine\Service\Addon\Controllers\Action\AbstractRoute;
use DebugToolbar\Toolbar\GarbageCollection;
use ExpressionEngine\Core\Provider;

class Act extends AbstractRoute
{
    public function __construct()
    {
        ee()->lang->loadfile('ee_debug_toolbar');
        ee()->load->add_package_path(PATH_THIRD . 'ee_debug_toolbar/');

        //run the garbage collection against the cache
        $gc = new GarbageCollection;
        $gc->run();
    }

    public function process()
    {
        $class = ee()->input->get_post('class');
        $method = ee()->input->get_post('method');

        //clean up the file so we know what package we're to include
        $package = strtolower(str_replace(array('_ext'), '', $class));

        $errors = true; //let's just assume the worst to keep us honest
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
                        $errors = false;
                        $this->$class->$method(); //paranoid but at least shit won't break.
                    }
                }
            }
        }

        if ($errors) {

            //use the new way
            $addon = ee('App')->get($package);
            if($addon instanceof Provider) {
                $namespace = $addon->getNamespace();
                echo $method;
                exit;
            }
            echo get_class($addon);
            exit;
            echo 'Ya dun goofed...';
            exit;
        }
    }
}
