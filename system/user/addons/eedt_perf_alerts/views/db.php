<div style="float:left">
	<h4><?php
        $log = ee('Database')->getLog();
        echo $log->getQueryCount().' '.lang('database_queries').' '.lang('total'); ?></h4>
</div>
<div style="float:right">
	<a href="javascript:;" id="EEDebug_slow_queries" class="EEDebug_actions">Slow Queries</a>
	| <a href="javascript:;" id="EEDebug_all_queries" class="EEDebug_actions">All Queries</a>
</div>

<br clear="all" />
<?php echo lang('mysql_query_cache_is'); ?> <?php echo($mysql_query_cache == 'y' ? lang('enabled') : lang('disabled')); ?>

<h4><?php echo lang('adapter'); ?> 0</h4>
<ol>
    <?php foreach ($log->getQueries() as $query): ?>
        <?php list($sql, $location, $time, $memory) = $query; ?>
	<li class="<?php echo ($time >= $settings['max_query_time'] ? 'EEDebug_slow_query' : 'EEDebug_normal_queries'); ?>">
        <strong>[<?php echo number_format($time, 4); ?>s / <?php echo ee('ee_debug_toolbar:ToolbarService')->filesize_format($memory); ?>]</strong> <?php echo $sql; ?>
    </li>
	<?php endforeach; ?>
</ol>