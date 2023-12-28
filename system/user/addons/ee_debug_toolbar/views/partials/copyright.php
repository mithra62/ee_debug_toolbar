<h4><?php echo lang('ee_debug_toolbar_module_name'); ?> v<?php echo DEBUG_TOOLBAR_VERSION; ?></h4>

<p><?php echo APP_NAME . ' ' . APP_VER . ' ' . lang('build') . ' (' . APP_BUILD . ')'; ?> <br/>
    <strong><?php echo lang('contributors'); ?>:</strong><br>
    <?php
    $count = 1;
    $total = count($project_contributors);
    foreach($project_contributors AS $name => $url): ?>
        <a href="<?php echo $url; ?>" target="_blank"><?php echo $name; ?></a><?php if($total > $count) echo ','; ?>
    <?php
        $count++;
    endforeach; ?>
</p>