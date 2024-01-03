<div style="float:left">
    <h4><?php
        $log = ee('Database')->getLog();
        echo $log->getQueryCount() . ' ' . lang('database_queries') . ' ' . lang('total'); ?></h4>
</div>
<div style="float:right">
    <a href="javascript:;" id="EEDebug_slow_queries" class="EEDebug_actions">Slow</a>
    | <a href="javascript:;" id="EEDebug_duplicate_queries" class="EEDebug_actions">Duplicate</a>
    | <a href="javascript:;" id="EEDebug_all_queries" class="EEDebug_actions">All</a>
</div>

<br clear="all"/>
<?php echo lang('mysql_query_cache_is'); ?> <?php echo ($mysql_query_cache == 'y' ? lang('enabled') : lang('disabled')); ?>

<h4><?php echo lang('adapter'); ?> 0</h4>
<ol>
    <?php
    foreach ($log->getQueries() as $query): ?>
        <?php list($sql, $location, $time, $memory) = $query; ?>
        <li class="<?php echo($time >= $settings['max_query_time'] ? 'EEDebug_slow_query' : 'EEDebug_normal_queries'); ?>">
            <strong>[<?php echo number_format($time, 4); ?>s
                / <?php echo ee('ee_debug_toolbar:ToolbarService')->filesizeFormat($memory); ?>
                ]</strong> <?php echo $sql; ?> <br><?php echo $location; ?>
        </li>
    <?php endforeach; ?>

    <?php
    foreach ($log->getQueryMetrics() as $query): ?>

    <?php if($query['count'] >= 2): ?>
    <li class="EEDebug_duplicate_query">
        <?php echo $query['query']; ?> <br>
        x<?php echo $query['count']; ?> <br>
        <div class="Eedt_perf_alerts_duplicate_query"><pre><?php echo implode('<br>', $query['locations']); ?></pre></div>
    </li>
    <?php endif; ?>
    <?php endforeach; ?>
</ol>