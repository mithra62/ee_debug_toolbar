<h4><?php echo lang('headers'); ?></h4>

<div id="EEDebug_headers">
	<pre>
<?php
			foreach (array('HTTP_ACCEPT', 'HTTP_USER_AGENT', 'HTTP_CONNECTION', 'SERVER_PORT', 'SERVER_NAME', 'REMOTE_ADDR', 'SERVER_SOFTWARE', 'HTTP_ACCEPT_LANGUAGE', 'SCRIPT_NAME', 'REQUEST_METHOD', ' HTTP_HOST', 'REMOTE_HOST', 'CONTENT_TYPE', 'SERVER_PROTOCOL', 'QUERY_STRING', 'HTTP_ACCEPT_ENCODING', 'HTTP_X_FORWARDED_FOR') as $header) {
				$val = (isset($_SERVER[$header])) ? $_SERVER[$header] : '';
				echo $header . ' =&gt; ' . $val . '<br />';
			}
			?>
	</pre>
</div>
<h4>$_COOKIE</h4>

<div id="EEDebug_cookie">
	<pre><?php echo eedt_output_array($_COOKIE, 'no_cookie_vars'); ?></pre>
</div>
<h4>$_GET</h4>

<div id="EEDebug_get">
	<pre><?php echo eedt_output_array($_GET, 'no_get_vars'); ?></pre>
</div>

<h4>$_POST</h4>
<div id="EEDebug_post">
	<pre><?php echo eedt_output_array($_POST, 'no_post_vars'); ?></pre>
</div>

<h4><?php echo lang('ee_session'); ?></h4>
<div id="EEDebug_ee_session">
	<pre><?php echo eedt_output_array($session_data, 'no_session_vars'); ?></pre>
</div>