<div style="float:left">
    <h4><?php echo lang('config'); ?></h4>
</div>
<br clear="all">

<div class="Eedt_debug_config_panel_container EEDebug_config_vars">
    <?php if(count($config_data) >= 1): ?>
            <?php foreach($config_data AS $key => $value): ?>
            <?php if(in_array($key, $ee_overrides)): ?>
            <?php $key = '<a href="https://docs.expressionengine.com/latest/general/system-configuration-overrides.html#' . $key .'" target="_blank">' . $key . '</a>'; ?>
            <?php endif; ?>
            <span style="font-weight: bold;"><?=$key;?> -> </span>
            <?php
            if(is_array($value)) {
                echo '<br>' . ee('eedt:OutputService')->outputArray($value, 'no_config_vars');
            } else {
                echo $value;
            }
            ?>
            <hr>
            <?php endforeach; ?>
    <?php else: ?>
        <?=lang('no_config_vars'); ?>
    <?php endif; ?>

</div>
