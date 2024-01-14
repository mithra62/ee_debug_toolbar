<?php
$total_addon_files = count($included_file_data['third_party_addon']) + count($included_file_data['first_party_modules']);
?>
<div style="float:left">
    <h4><?php echo lang('file_information'); ?></h4>
</div>
<div style="float:right" id="Eedt_debug_files_panel_nav_items">
    <a href="javascript:;" id="EEDebug_general_files" class="flash">General</a>
    | <a href="javascript:;" id="EEDebug_addon_files" class="">Add-ons </a>
    | <a href="javascript:;" id="EEDebug_ee_files" class="">EE </a>
    | <a href="javascript:;" id="EEDebug_composer_files" class="">Composer </a>
    | <a href="javascript:;" id="EEDebug_other_files" class=""><?php echo lang('other_files'); ?></a>
</div>
<br clear="all">
<br clear="all">

<div class="Eedt_debug_files_panel_container EEDebug_general_files">
    <h4><?php echo lang('requested_url'); ?></h4>
    <pre><?php echo $_SERVER['REQUEST_URI']; ?></pre><br>

    <?php
    $templates_used = ee('ee_debug_toolbar:TrackerService')->getAllTemplates();
    if($templates_used) {
        echo '<h4>' . lang('eedt.templates_used') . '</h4>';
        foreach($templates_used AS $template) {
            if($template['template_name']) {
                echo '<a href="' . ee()->config->item('cp_url') . '?/cp/design/template/edit/' . $template['template_id'] . '" target="_blank">' .
                    $template_groups[$template['group_id']] . '/' . $template['template_name'] . '</a><br>';
            }
        }

        echo '<br>';
    }
    ?>
    <h4><?php echo lang('system_paths'); ?></h4>

    <span class="label"><?php echo lang('app'); ?>:</span> <code><?php echo realpath(APPPATH); ?></code><br>
    <span class="label"><?php echo lang('sysdir'); ?>:</span> <code><?php echo realpath(SYSDIR); ?></code><br>
    <span class="label"><?php echo lang('fcpath'); ?>:</span> <code><?php echo realpath(FCPATH); ?></code><br>
    <span class="label"><?php echo lang('syspath'); ?>:</span> <code><?php echo realpath(SYSPATH); ?></code><br>
    <span class="label"><?php echo lang('self'); ?>:</span> <code><?php echo realpath(SELF); ?></code><br>
    <span class="label"><?php echo lang('basepath'); ?>:</span> <code><?php echo realpath(BASEPATH); ?></code><br>
    <span class="label"><?php echo lang('themes'); ?>:</span> <code><?php echo realpath(PATH_THEMES); ?></code><br>
    <span class="label"><?php echo lang('third_party'); ?>:</span> <code><?php echo realpath(PATH_THIRD); ?></code><br>
    <span class="label"><?php echo lang('member_themes'); ?>:</span> <code><?php echo realpath(PATH_MBR_THEMES); ?></code>
    <br>
    <?php if (defined('PATH_JAVASCRIPT')): ?>
        <span class="label"><?php echo lang('javascript'); ?>:</span> <code><?php echo realpath(PATH_JAVASCRIPT); ?></code>
        <br>
    <?php endif; ?>
</div>

<div class="Eedt_debug_files_panel_container EEDebug_addon_files" style="display: none">
<?php
echo '<h4>Add-ons (' . $total_addon_files . ')</h4><pre>';
foreach ($included_file_data['third_party_addon'] as $file) {
    echo $file . '<br />';
}
foreach ($included_file_data['first_party_modules'] as $file) {
    echo $file . '<br />';
}
?>
</div>

<div class="Eedt_debug_files_panel_container EEDebug_ee_files" style="display: none">
    <?php
    $total = count($included_file_data['expressionengine_core']);
    echo '<h4>EE (' . $total . ')</h4><pre>';
    foreach ($included_file_data['expressionengine_core'] as $file) {
        echo $file . '<br />';
    }
    ?>
</div>

<div class="Eedt_debug_files_panel_container EEDebug_composer_files" style="display: none">
    <?php
    $total = count($included_file_data['composer']);
    echo '<h4>Composer (' . $total . ')</h4><pre>';
    foreach ($included_file_data['composer'] as $file) {
        echo $file . '<br />';
    }
    ?>
</div>

<div class="Eedt_debug_files_panel_container EEDebug_other_files" style="display: none">
    <?php
    $total = count($included_file_data['other_files']);
    echo '<h4>Other Files (' . $total . ')</h4><pre>';
    foreach ($included_file_data['other_files'] as $file) {
        echo $file . '<br />';
    }
    ?>
</div>
