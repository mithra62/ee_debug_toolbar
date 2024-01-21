<?php

namespace DebugToolbar\Actions;

class GetPanelData extends AbstractAction
{
    public function processDebug()
    {
        $panel = ee()->input->get('panel', false);
        if (!$panel) {
            return;
        }

        //the cache file is just an XML so we check for existence, node, and display. easy
        $file = $this->cache_dir . $this->toolbar->makeCacheFilename() . '.gz';

        if (file_exists($file) && is_readable($file)) {
            $gz = gzfile($file);
            $gz = implode("", $gz);
            $xml = simplexml_load_string($gz);
            $panel_node = $panel . '_panel';
            if (isset($xml->panels->$panel_node->output) && $xml->panels->$panel_node->output != '') {
                echo base64_decode($xml->panels->$panel_node->output);
            }
            exit;
        }
    }
}
