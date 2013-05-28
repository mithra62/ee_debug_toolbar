<div style="float:left">
	<h4><?php echo count($query_data['queries']).' '.lang('database_queries').' '.lang('total'); ?></h4>
</div>
<div style="float:right">
	<a href="javascript:;" id="EEDebug_slow_queries" class="EEDebug_actions">Slow Queries</a>
	| <a href="javascript:;" id="EEDebug_all_queries" class="EEDebug_actions">All Queries</a>
</div>

<br clear="all" />
<?php echo lang('query_cache_is'); ?> <?php echo ($config_data['enable_db_caching'] == 'y' ? lang('enabled') : lang('disabled')); ?><br />
<?php echo lang('mysql_query_cache_is'); ?> <?php echo ($mysql_query_cache == 'y' ? lang('enabled') : lang('disabled')); ?>
<h4><?php echo lang('adapter'); ?> 0</h4>
<ol>
	<?php foreach ($query_data['queries'] AS $query): ?>
	<li class="<?php echo ($query['time'] >= $settings['max_query_time'] ? 'EEDebug_slow_query' : 'EEDebug_normal_queries'); ?>"><strong>[<?php echo $query['time']; ?> s]</strong> <?php echo $query['query']; ?></li>
	<?php endforeach; ?>
</ol>