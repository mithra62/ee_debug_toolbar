<?php

namespace DebugToolbar\Actions;

class PanelAjax extends AbstractAction
{
    public function process()
    {
        echo __FILE__ . ':' . __LINE__;
        exit;

        $data = [];
        $panel = ee()->input->get("panel", false);
        $method = ee()->input->get("method", false);

        if (!$panel || $method) {
            return;
        }

        if (in_array($panel, $this->panel_order)) {
            //Native Panel
            ee()->load->file(PATH_THIRD . 'eedt/panels/Eedt_' . $panel . '_panel.php');
            $class = 'Eedt_' . $panel . '_panel';

            if (class_exists($class)) {

                $instance = new $class();

                if (method_exists($instance, $method)) {
                    $data = $instance->$method();
                }
            }


        } else {
            //Third Party panel

            /**
             * TODO
             * I realise now that we need to somehow specify the path to the class since
             * the panel name will not necessarily match up with the extension name, so we cant
             * make that assumption.
             */
        }

        if ($data) {
            ee()->output->send_ajax_response($data);
        }
    }
}
