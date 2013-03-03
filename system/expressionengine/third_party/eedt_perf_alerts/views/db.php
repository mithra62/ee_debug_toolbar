	<div style="float:left">
		<h4><?php echo count($query_data['queries']).' '.lang('database_queries'); ?></h4>
	</div>
	<div style="float:right">
		<a href="javascript:;" id="EEDebug_slow_queries" class="EEDebug_actions">Slow Queries</a>
		| <a href="javascript:;" id="EEDebug_all_queries" class="EEDebug_actions">All Queries</a>
	</div>
	
	<br clear="all" />
	<?php echo lang('query_cache_is'); ?> <?php echo ($config_data['enable_db_caching'] == 'y' ? lang('enabled') : lang('disabled')); ?>
	<h4><?php echo lang('adapter'); ?> 0</h4>
	<ol>
		<?php foreach ($query_data['queries'] AS $query): ?>
		<li class="<?php echo ($query['time'] >= 0.01 ? 'EEDebug_slow_query' : 'EEDebug_normal_queries'); ?>"><strong>[<?php echo $query['time']; ?> s]</strong> <?php echo $query['query']; ?></li>
		<?php endforeach; ?>
	</ol>
	<script src="<?php echo $perf_theme_js_url . "perf_alerts.js" ?>" type="text/javascript"
			charset="utf-8" defer id="EEDebug_debug_perf_alerts_script"></script>	