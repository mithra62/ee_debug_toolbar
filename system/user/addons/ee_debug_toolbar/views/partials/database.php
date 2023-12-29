<h4><?php
    $log = ee('Database')->getLog();
    echo $log->getQueryCount() . ' ' . lang('database_queries'); ?></h4>
<br/>
<?php echo lang('mysql_query_cache_is'); ?> <?php echo($mysql_query_cache == 'y' ? lang('enabled') : lang('disabled')); ?>
<h4><?php echo lang('adapter'); ?> 0</h4>
<ol>
    <?php foreach ($log->getQueries() as $query): ?>
        <?php list($sql, $location, $time, $memory) = $query; ?>
        <li><strong>[<?php echo number_format($time, 4); ?>s / <?php echo ee('ee_debug_toolbar:ToolbarService')->filesizeFormat($memory); ?>
                ]</strong>
            <?php echo $sql; ?> <br><?php echo $location; ?>
        </li>
    <?php endforeach; ?>
</ol>