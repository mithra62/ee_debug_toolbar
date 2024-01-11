<div style="float:left">
    <h4><?php
        $log = ee('Database')->getLog();
        echo $log->getQueryCount() . ' ' . lang('database_queries') . ' ' . lang('total'); ?></h4>
</div>
<div style="float:right" id="Eedt_debug_database_panel_nav_items">
    <a href="javascript:;" id="EEDebug_slow_queries" class="">Slow</a>
    | <a href="javascript:;" id="EEDebug_duplicate_queries" class="">Duplicate</a>
    | <a href="javascript:;" id="EEDebug_all_queries" class=" flash">All</a>
</div>

<br clear="all"/>
<?php echo lang('mysql_query_cache_is'); ?> <?php echo ($mysql_query_cache == 'y' ? lang('enabled') : lang('disabled')); ?>

<h4><?php echo lang('adapter'); ?> 0</h4>
<div class="Eedt_debug_database_panel_container EEDebug_normal_queries">
<?php
$count = 1;
foreach ($log->getQueries() as $query): ?>
    <?php
    list($sql, $location, $time, $memory) = $query;
    $class = 'nice';
    if($time >= $settings['max_query_time']) {
        $class = 'flash';
    }
    ?>
        <?php echo $count; ?>. <strong>[<span class="<?php echo $class; ?>">
                <?php echo number_format($time, 4); ?>s
                / <?php echo ee('ee_debug_toolbar:ToolbarService')->filesizeFormat($memory); ?></span>
            ]</strong> <code><?php echo $sql; ?></code> <br><pre><?php echo $location; ?></pre>
        <br>
<?php
    $count++;
endforeach; ?>
</div>

<div class="Eedt_debug_database_panel_container EEDebug_slow_query" style="display: none">
<?php
$count = 1;
$found = false;
foreach ($log->getQueries() as $query): ?>
    <?php
    list($sql, $location, $time, $memory) = $query;
    if($time >= $settings['max_query_time']):
    ?>
    <div class="">
        <?php echo $count; ?>. <strong>[<?php echo number_format($time, 4); ?>s
            / <?php echo ee('ee_debug_toolbar:ToolbarService')->filesizeFormat($memory); ?>
            ]</strong> <code><?php echo $sql; ?></code> <br><pre><?php echo $location; ?></pre><br>
    </div>
<?php
        $found = true;
        $count++;
endif;
endforeach; ?>
    <?php if(!$found): ?>
        <br>No Slow Queries
    <?php endif; ?>
</div>

<div class="Eedt_debug_database_panel_container EEDebug_duplicate_query" style="display: none">
<?php
$count = 1;
$found = false;
foreach ($log->getQueryMetrics() as $query): ?>

    <?php if($query['count'] >= 2): ?>
        <div class="">
            <?php echo $count .'. <code>' . $query['query']; ?></code> <br>
            x<?php echo $query['count']; ?> <br>
            <div class="Eedt_perf_alerts_duplicate_query">
                <?php foreach($query['locations'] AS $location): ?>
                    <pre><?php echo $location; //echo implode('<br>', $query['locations']); ?></pre><br>
                <?php endforeach; ?>
            </div>
        </div>

    <?php
        $found = true;
        $count++;
    endif; ?>
<?php endforeach; ?>
    <?php if(!$found): ?>
        <br>No Duplicate Queries
    <?php endif; ?>
</div>
