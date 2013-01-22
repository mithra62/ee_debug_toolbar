
<link rel="stylesheet" type="text/css" href="<?php echo $theme_url."css/ee_debug_toolbar.css" ?>">

<div id="EEDebug_debug">
<div id="EEDebug_copyright" class="EEDebug_panel">
	<h4><?php echo lang('ee_debug_toolbar_module_name'); ?> v<?php echo $ext_version; ?></h4>

	<p><?php echo APP_NAME . ' ' . APP_VER . ' ' . lang('build') . ' (' . APP_BUILD . ')'; ?> <br/>
		CodeIgniter Version: <?php echo CI_VERSION; ?></p>
</div>
<div id="EEDebug_variables" class="EEDebug_panel">
	<h4><?php echo lang('headers'); ?></h4>

	<div id="ZFDebug_headers">
		<div class="pre">
			<?php
			foreach (array('HTTP_ACCEPT', 'HTTP_USER_AGENT', 'HTTP_CONNECTION', 'SERVER_PORT', 'SERVER_NAME', 'REMOTE_ADDR', 'SERVER_SOFTWARE', 'HTTP_ACCEPT_LANGUAGE', 'SCRIPT_NAME', 'REQUEST_METHOD', ' HTTP_HOST', 'REMOTE_HOST', 'CONTENT_TYPE', 'SERVER_PROTOCOL', 'QUERY_STRING', 'HTTP_ACCEPT_ENCODING', 'HTTP_X_FORWARDED_FOR') as $header) {
				$val = (isset($_SERVER[$header])) ? $_SERVER[$header] : '';
				echo $header . ' =&gt; ' . $val . '<br />';
			}
			?>
		</div>
	</div>
	<h4>$_COOKIE</h4>

	<div id="ZFDebug_cookie">
		<div class="pre">
			<?php
			if (count($_COOKIE) == '0') {
				echo lang('no_cookie_vars');
			} else {
				foreach ($_COOKIE AS $key => $value) {
					echo $key . ' =&gt; ' . $value . '<br />';
				}
			}
			?>
		</div>
	</div>
	<h4>$_GET</h4>

	<div id="ZFDebug_get">
		<div class="pre">
			<?php
			if (count($_GET) == '0') {
				echo lang('no_get_vars');
			} else {
				foreach ($_GET AS $key => $value) {
					echo $key . ' =&gt; ' . $value . '<br />';
				}
			}
			?>
		</div>
	</div>
	<h4>$_POST</h4>

	<div id="ZFDebug_post">
		<div class="pre">
			<?php
			if (count($_POST) == '0') {
				echo lang('no_post_vars');
			} else {
				foreach ($_POST AS $key => $value) {
					echo $key . ' =&gt; ' . $value . '<br />';
				}
			}
			?>
		</div>
	</div>
	<h4><?php echo lang('ee_session'); ?></h4>

	<div id="ZFDebug_ee_session">
		<div class="pre">
			<?php
			if (count($session_data) == '0') {
				echo lang('no_session_vars');
			} else {
				foreach ($session_data AS $key => $value) {
					echo $key . ' =&gt; ' . $value . '<br />';
				}
			}
			?>
		</div>
	</div>
</div>
<div id="EEDebug_file" class="EEDebug_panel">
	<h4><?php echo lang('file_information'); ?></h4>
	<?php echo (count(get_included_files()) + 1); //faked for included graph file below ?> <?php echo lang('files_included'); ?>
	<br>

	<h4><?php echo lang('system_paths'); ?></h4>

	<?php echo lang('bootstrap_file'); ?>: <?php echo realpath($included_file_data['bootstrap_file']); ?><br>
	<?php echo lang('app'); ?>: <?php echo realpath(APPPATH); ?><br>
	<?php echo lang('themes'); ?>: <?php echo realpath(PATH_THEMES); ?><br>
	<?php echo lang('third_party'); ?>: <?php echo realpath(PATH_THIRD); ?><br>
	<?php echo lang('member_themes'); ?>: <?php echo realpath(PATH_MBR_THEMES); ?><br>
	<?php if (defined('PATH_JAVASCRIPT')): ?>
	<?php echo lang('javascript'); ?>: <?php echo realpath(PATH_JAVASCRIPT); ?><br>
	<?php endif; ?>

	<?php
	foreach ($included_file_data AS $section => $files) {
		if (is_array($files) && count($files) >= '1') {
			echo '<h4>' . lang($section) . ' (' . count($files) . ')</h4><div class="pre">';
			foreach ($files AS $file) {
				echo $file . '<br />';
			}
			echo '</div>';
		}
	}
	?>
</div>
<?php if ($this->input->get("D", FALSE) != 'cp'): ?>
	<div style="display: none;" id="EEDebug_memory" class="EEDebug_panel">
	<?php else: ?>
	<div style="display: none;" id="EEDebug_memory_cp" class="EEDebug_panel">
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
	<div id="EEDebug_template_list" style="display:none">
		<?php
		foreach ($template_debugging AS $log) {
			echo '(' . $log['time'] . '/' . $log['memory'] . ') - ' . $log['desc'] . '<br />';
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

		<div class="pre">
			<?php
			foreach ($config_data AS $key => $value) {
				if (!is_array($value)) {
					echo $key . ' =&gt; ' . $value . ' <br />';

				} else {
					echo '<div class="pre">' . $key . '=&gt;';
					foreach ($value AS $_k => $_v) {
						echo $_k . ' =&gt; ' . print_r($_v, TRUE) . ' <br />';
					}
					echo '</div>';
				}
			}
			?>
		</div>
	</div>
	<div id="EEDebug_database" class="EEDebug_panel">
		<h4><?php echo lang('database_queries'); ?></h4>
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
			<img src="<?php echo $theme_url."images/logo.png" ?>"
				 style="vertical-align:middle" alt="<?php echo lang('credits'); ?>"
				 title="<?php echo lang('credits'); ?>">  v<?=APP_VER?> <?php //echo ' - '; echo lang('build'). '&nbsp;'.APP_BUILD;?>
			/<?php echo phpversion(); ?>
		</span>
		<span class="EEDebug_span clickable" data-target="EEDebug_variables">
			<img src="<?php echo $theme_url."images/variables.png" ?>"
				 style="vertical-align:middle" alt="<?php echo lang('variables'); ?>"
				 title="<?php echo lang('variables'); ?>">  <?php echo lang('variables'); ?>
		</span>
		<span class="EEDebug_span clickable" data-target="EEDebug_file">
			<img src="<?php echo $theme_url."images/files.png" ?>"
				 style="vertical-align:middle" alt="<?php echo lang('files'); ?>"
				 title="<?php echo lang('files'); ?>"> <?php echo count(get_included_files()); ?> <?php echo lang('files'); ?>
		</span>

		<?php if ($this->input->get("D", FALSE) != 'cp'): ?>
		<span class="EEDebug_span clickable" data-target="EEDebug_memory">
		<?php else: ?>
		<span class="EEDebug_span clickable" data-target="EEDebug_memory_cp">
		<?php endif; ?>
		<img src="<?php echo $theme_url."images/memory.png" ?>"
			 style="vertical-align:middle" alt="<?php echo lang('memory'); ?>"
			 title="<?php echo lang('memory'); ?>"> <?php echo $memory_usage; ?>
		of <?php echo ini_get('memory_limit'); ?>
	</span>
		<span class="EEDebug_span clickable" data-target="EEDebug_time">
			<img src="<?php echo $theme_url."images/time.png" ?>"
				 style="vertical-align:middle" alt="<?php echo lang('time'); ?>"
				 title="<?php echo lang('time'); ?>"> <?php echo $elapsed_time; ?>s
		</span>
		<span class="EEDebug_span clickable" data-target="EEDebug_registry">
			<img src="<?php echo $theme_url."images/config.png" ?>"
				 style="vertical-align:middle" alt="<?php echo lang('configuration_data'); ?>"
				 title="<?php echo lang('configuration_data'); ?>">  <?php echo lang('config'); ?>
			(<?php echo count($config_data); ?>)
		</span>
		<span class="EEDebug_span clickable" data-target="EEDebug_database">
			<img src="<?php echo $theme_url."images/db.png" ?>"
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
<script src="<?php echo $theme_url . "js/ee_debug_toolbar.js" ?>" type="text/javascript"
		charset="utf-8" defer id="EEDebug_debug_script"></script>
