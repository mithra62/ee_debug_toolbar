<div id="EEDebug_variables" class="EEDebug_panel">
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
		<pre>
<?php
			if (count($_COOKIE) == '0') {
				echo lang('no_cookie_vars');
			} else {
				foreach ($_COOKIE AS $key => $value) {
					echo $key . ' =&gt; ' . $value . '<br />';
				}
			}
			?>
		</pre>
	</div>
	<h4>$_GET</h4>

	<div id="EEDebug_get">
		<pre>
<?php
			if (count($_GET) == '0') {
				echo lang('no_get_vars');
			} else {
				foreach ($_GET AS $key => $value) {
					echo $key . ' =&gt; ' . $value . '<br />';
				}
			}
			?>
		</pre>
	</div>
	<h4>$_POST</h4>

	<div id="EEDebug_post">
		<pre>
<?php
			if (count($_POST) == '0') {
				echo lang('no_post_vars');
			} else {
				foreach ($_POST AS $key => $value) {
					echo $key . ' =&gt; ' . $value . '<br />';
				}
			}
			?>
		</pre>
	</div>
	<h4><?php echo lang('ee_session'); ?></h4>

	<div id="EEDebug_ee_session">
		<pre>
<?php
			if (count($session_data) == '0') {
				echo lang('no_session_vars');
			} else {
				foreach ($session_data AS $key => $value) {
					echo $key . ' =&gt; ' . $value . '<br />';
				}
			}
			?>
		</pre>
	</div>
</div>