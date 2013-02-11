
<link rel="stylesheet" type="text/css" href="<?php echo $theme_css_url."ee_debug_toolbar.css" ?>">

<div id="EEDebug_debug">
<div id="EEDebug_copyright" class="EEDebug_panel">
	<h4><?php echo lang('ee_debug_toolbar_module_name'); ?> v<?php echo $ext_version; ?></h4>

	<p><?php echo APP_NAME . ' ' . APP_VER . ' ' . lang('build') . ' (' . APP_BUILD . ')'; ?> <br/>
		CodeIgniter <?php echo ucfirst(lang('version')); ?>: <?php echo CI_VERSION; ?><br />
		<?php echo lang('contributors'); ?> <?php echo lang('contributor_list'); ?></p>
</div>
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
<div id="EEDebug_file" class="EEDebug_panel">
	<h4><?php echo lang('file_information'); ?></h4>
	<?php echo (count(get_included_files()) + 1); //faked for included graph file below ?> <?php echo lang('files_included'); ?>
	<br>

	<h4><?php echo lang('system_paths'); ?></h4>

	<span class="label"><?php echo lang('bootstrap_file'); ?>:</span> <code><?php echo realpath($included_file_data['bootstrap_file']); ?></code><br>
	<span class="label"><?php echo lang('app'); ?>:</span> <code><?php echo realpath(APPPATH); ?></code><br>
	<span class="label"><?php echo lang('themes'); ?>:</span> <code><?php echo realpath(PATH_THEMES); ?></code><br>
	<span class="label"><?php echo lang('third_party'); ?>:</span> <code><?php echo realpath(PATH_THIRD); ?></code><br>
	<span class="label"><?php echo lang('member_themes'); ?>:</span> <code><?php echo realpath(PATH_MBR_THEMES); ?></code><br>
	<?php if (defined('PATH_JAVASCRIPT')): ?>
	<span class="label"><?php echo lang('javascript'); ?>:</span> <code><?php echo realpath(PATH_JAVASCRIPT); ?></code><br>
	<?php endif; ?>

	<?php
	foreach ($included_file_data AS $section => $files) {
		if (is_array($files) && count($files) >= '1') {
			echo '<h4>' . lang($section) . ' (' . count($files) . ')</h4><pre>';
			foreach ($files AS $file) {
				echo $file . '<br />';
			}
			echo '</pre>';
		}
	}
	?>
</div>
<?php if ($this->input->get("D", FALSE) != 'cp'): ?>
	<div style="" id="EEDebug_memory" class="EEDebug_panel show_graph">
	<?php else: ?>
	<div style="" id="EEDebug_memory_cp" class="EEDebug_panel show_graph">
	<?php endif; ?>
<?php if ($template_debugging_enabled): ?>
	<div style="float:left">
		<h4><?php echo lang('template_debugging');?></h4>
	</div>
	<div style="float:right">
		<a href="javascript:;" id="EEDebug_graph_display" class="EEDebug_graph_actions EEDebug_graph_action_active">Graph</a>
		| <a href="javascript:;" id="EEDebug_graph_list" class="EEDebug_graph_actions">List</a>
	</div>
	<br clear="all"/>
	<div id="EEDebug_graph"></div>
	<div id="EEDebug_template_list" style="">
		<?php
		$total = 0;
		foreach ($template_debugging AS $log) {
			echo "\n<div id='EEDebug_hash_$total'>";
			echo '(' . $log['time'] . '/' . $log['memory'] . 'MB) - ' . $log['desc'] . '<br />';
			echo "</div>";
			$total++;
		}
		?>
	</div>
	<?php else: ?>
	<h4><?php echo lang('templates_not_enabled');?></h4>
	<?php endif; ?>
</div>
	<div id="EEDebug_time" class="EEDebug_panel">
		<h4><?php echo lang('benchmarks'); ?></h4>
		<?php
		foreach ($benchmark_data AS $key => $value) {
			$key = ucwords(str_replace(array('_', '-'), ' ', $key));
			echo $key . ': ' . $value . '<br />';
		}
		?>
	</div>
	<div id="EEDebug_registry" class="EEDebug_panel">
		<h4><?php echo lang('configuration'); ?></h4>
		<pre><?php
			foreach ($config_data AS $key => $value) {
			
				if (!is_array($value)) {
					echo $key . ' =&gt; ' . $value . ' <br />';

				} else {
					echo '<pre>' . $key . '=&gt;';
					foreach ($value AS $_k => $_v) {
						echo $_k . ' =&gt; ' . print_r($_v, TRUE) . ' <br />';
					}
					echo '</pre>';
				}
			}
			?>
		</pre>
	</div>
	<div id="EEDebug_database" class="EEDebug_panel">
		<h4><?php echo count($query_data['queries']).' '.lang('database_queries'); ?></h4>
		<?php echo lang('query_cache_is'); ?> <?php echo ($config_data['enable_db_caching'] == 'y' ? lang('enabled') : lang('disabled')); ?>
		<h4><?php echo lang('adapter'); ?> 0</h4>
		<ol>
			<?php foreach ($query_data['queries'] AS $query): ?>
			<li><strong>[<?php echo $query['time']; ?> s]</strong> <?php echo $query['query']; ?></li>
			<?php endforeach; ?>
		</ol>
	</div>
	<div id="EEDebug_info">
		<span class="EEDebug_span clickable" data-target="EEDebug_copyright">
			<img src="<?php echo $theme_img_url."logo.png" ?>"
				 style="vertical-align:middle" alt="<?php echo lang('credits'); ?>"
				 title="<?php echo lang('credits'); ?>">  v<?=APP_VER?> <?php //echo ' - '; echo lang('build'). '&nbsp;'.APP_BUILD;?>
			/<?php echo phpversion(); ?>
		</span>
		<span class="EEDebug_span clickable" data-target="EEDebug_variables">
			<img src="<?php echo $theme_img_url."variables.png" ?>"
				 style="vertical-align:middle" alt="<?php echo lang('variables'); ?>"
				 title="<?php echo lang('variables'); ?>">  <?php echo lang('variables'); ?>
		</span>
		<span class="EEDebug_span clickable" data-target="EEDebug_file">
			<img src="<?php echo $theme_img_url."files.png" ?>"
				 style="vertical-align:middle" alt="<?php echo lang('files'); ?>"
				 title="<?php echo lang('files'); ?>"> <?php echo count(get_included_files()); ?> <?php echo lang('files'); ?>
		</span>

		<?php if ($this->input->get("D", FALSE) != 'cp'): ?>
		<span class="EEDebug_span clickable" data-target="EEDebug_memory">
		<?php else: ?>
		<span class="EEDebug_span clickable" data-target="EEDebug_memory_cp">
		<?php endif; ?>
		<img src="<?php echo $theme_img_url."memory.png" ?>"
			 style="vertical-align:middle" alt="<?php echo lang('memory'); ?>"
			 title="<?php echo lang('memory'); ?>"> <?php echo $memory_usage; ?>
		of <?php echo ini_get('memory_limit'); ?>
	</span>
		<span class="EEDebug_span clickable" data-target="EEDebug_time">
			<img src="<?php echo $theme_img_url."time.png" ?>"
				 style="vertical-align:middle" alt="<?php echo lang('time'); ?>"
				 title="<?php echo lang('time'); ?>"> <?php echo $elapsed_time; ?>s
		</span>
		<span class="EEDebug_span clickable" data-target="EEDebug_registry">
			<img src="<?php echo $theme_img_url."config.png" ?>"
				 style="vertical-align:middle" alt="<?php echo lang('configuration_data'); ?>"
				 title="<?php echo lang('configuration_data'); ?>">  <?php echo lang('config'); ?>
			(<?php echo count($config_data); ?>)
		</span>
		<span class="EEDebug_span clickable" data-target="EEDebug_database">
			<img src="<?php echo $theme_img_url."db.png" ?>"
				 style="vertical-align:middle" alt="<?php echo lang('database'); ?>"
				 title="<?php echo lang('database'); ?>"> <?php echo $query_count; ?> <?php echo lang('in'); ?> <?php echo $query_data['total_time']; ?>
			s
		</span>
		<span class="EEDebug_span EEDebug_last clickable" id="EEDebug_toggler">&#171;</span>
	</div>
</div>

<script type="text/javascript">
	window.EEDebug = {data:{}, config:{}};
	window.EEDebug.config.template_debugging_enabled = <?php if($template_debugging_enabled){ echo "true"; }else{ echo "false";}?>;
	window.EEDebug.data.tmpl_data = <?php echo $template_debugging_chart_json?>;
</script>
<script src="<?php echo $theme_js_url . "ee_debug_toolbar.js" ?>" type="text/javascript"
		charset="utf-8" defer id="EEDebug_debug_script"></script>
