<div style="float:left">
    <h4><?php echo lang('variables'); ?></h4>
</div>
<div style="float:right" id="Eedt_debug_variables_panel_nav_items">
    <a href="javascript:;" id="EEDebug_get" class="flash">$_GET</a>
    | <a href="javascript:;" id="EEDebug_post" class="">$_POST</a>
    | <a href="javascript:;" id="EEDebug_headers" class="">$_SERVER</a>
    | <a href="javascript:;" id="EEDebug_cookie" class="">$_COOKIE</a>
    | <a href="javascript:;" id="EEDebug_php_session" class="">$_SESSION</a>
    | <a href="javascript:;" id="EEDebug_ee_session" class=""><?php echo lang('ee_session'); ?></a>
</div>
<br clear="all">


<div class="Eedt_debug_variables_panel_container EEDebug_get">
    <h4>$_GET</h4>
    <pre><?php echo ee('ee_debug_toolbar:OutputService')->outputArray($_GET, 'no_get_vars'); ?></pre>
</div>

<div class="Eedt_debug_variables_panel_container EEDebug_post" style="display: none">
    <h4>$_POST</h4>
    <pre><?php echo ee('ee_debug_toolbar:OutputService')->outputArray($_POST, 'no_post_vars'); ?></pre>
</div>


<div id="EEDebug_headers" class="Eedt_debug_variables_panel_container EEDebug_headers" style="display: none">
    <h4>$_SERVER</h4>
	<pre>
<?php
foreach (array('HTTP_ACCEPT', 'HTTP_USER_AGENT', 'HTTP_CONNECTION', 'SERVER_PORT', 'SERVER_NAME', 'REMOTE_ADDR', 'SERVER_SOFTWARE', 'HTTP_ACCEPT_LANGUAGE', 'SCRIPT_NAME', 'REQUEST_METHOD', ' HTTP_HOST', 'REMOTE_HOST', 'CONTENT_TYPE', 'SERVER_PROTOCOL', 'QUERY_STRING', 'HTTP_ACCEPT_ENCODING', 'HTTP_X_FORWARDED_FOR') as $header) {
    $val = (isset($_SERVER[$header])) ? $_SERVER[$header] : '';
    echo $header . ' =&gt; ' . $val . '<br />';
}
?>
	</pre>
</div>

<div id="EEDebug_cookie" class="Eedt_debug_variables_panel_container EEDebug_cookie" style="display: none">
    <h4>$_COOKIE</h4>
	<pre><?php echo ee('ee_debug_toolbar:OutputService')->outputArray($_COOKIE, 'no_cookie_vars'); ?></pre>
</div>

<div class="Eedt_debug_variables_panel_container EEDebug_ee_session" style="display: none">
    <h4><?php echo lang('ee_session'); ?></h4>
	<pre><?php echo ee('ee_debug_toolbar:OutputService')->outputArray($session_data, 'no_session_vars'); ?></pre>
</div>

<div class="Eedt_debug_variables_panel_container EEDebug_php_session" style="display: none">
    <h4>$_SESSION</h4>
    <pre><?php
        echo ee('ee_debug_toolbar:OutputService')->outputArray($_SESSION, 'no_session_vars'); ?></pre>
</div>