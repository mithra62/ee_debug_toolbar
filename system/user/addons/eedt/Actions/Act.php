<?php

namespace DebugToolbar\Actions;

use DebugToolbar\Exceptions\InvalidActionCallException;
use ExpressionEngine\Service\Addon\Addon;

class Act extends AbstractAction
{
    /**
     * @return void
     * @throws InvalidActionCallException
     */
    public function process()
    {
        if (!ee('eedt:ToolbarService')->canViewToolbar()) {
            return;
        }

        $class = ee()->input->get_post('class');
        $method = ee()->input->get_post('method');

        //clean up the file so we know what package we're to include
        $package = strtolower(str_replace(['_ext'], '', $class));

        $errors = true; //let's just assume the worst to keep us honest
        if ($this->toolbar->isAddonInstalled($package)) {
            $provider = ee('Addon')->get($package);
            if ($provider instanceof Addon) {
                ee()->load->add_package_path(PATH_THIRD . $package);
                $namespace = $provider->getNamespace();
                $class = $namespace . '\\Actions\\' . $method;
                if (class_exists($class)) {
                    $obj = new $class;
                    if ($obj instanceof AbstractAction) {
                        $obj->processDebug();
                        $errors = false;
                    }
                }
            }
        }

        if ($errors) {
            throw new InvalidActionCallException("Invalid Debug Action Request!");
        }
    }

    /**
     * @return mixed
     * @throws InvalidActionCallException
     */
    public function processDebug()
    {
        throw new InvalidActionCallException("Invalid Debug Action Request!");
    }
}
