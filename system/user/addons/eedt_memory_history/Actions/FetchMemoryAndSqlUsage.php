<?php

namespace DebugToolbar\MemoryHistory\Actions;

use DebugToolbar\Actions\AbstractAction;

class FetchMemoryAndSqlUsage extends AbstractAction
{
    public function processDebug()
    {
        $session_id = ee()->session->userdata['session_id'];
        $is_cp = ee()->input->get('cp') == 'y' ? 'y' : 'n';
        $data = ee()->db->where("session_id", $session_id)
            ->where('cp', $is_cp)
            ->limit(20)
            ->order_by("timestamp", "desc")
            ->get("eedt_memory_history")
            ->result_array();

        //Garbage collect
        ee()->db->where("timestamp < ", ee()->localize->now - 14400)->delete("eedt_memory_history"); //4 hours
        ee()->output->send_ajax_response($data);
    }
}
