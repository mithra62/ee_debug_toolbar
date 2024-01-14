<div style="float:left"><h4><?php //echo lang('eedt_cartthrob_module_name'); ?></h4></div>

<div style="float:right" id="EEDebug_errors_nav_items">
    <a href="javascript:;" id="EEDebug_errors_clear_errors" class=" flash">Clear Errors</a>
</div>

<?php
echo ee('ee_debug_toolbar:OutputService')->outputArray($errors, 'no_errors'); ?>