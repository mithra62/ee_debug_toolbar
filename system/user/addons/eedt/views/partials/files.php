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
    $templates_used = ee('eedt:TrackerService')->getAllTemplates();
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
    <h4><?php echo lang('eedt.path_constants'); ?></h4>

    <span class="label">APPPATH:</span> <code><?php echo realpath(APPPATH); ?></code><br>
    <span class="label">SYSDIR:</span> <code><?php echo realpath(SYSDIR); ?></code><br>
    <span class="label">FCPATH:</span> <code><?php echo realpath(FCPATH); ?></code><br>
    <span class="label">SYSPATH:</span> <code><?php echo realpath(SYSPATH); ?></code><br>
    <span class="label">EESELF:</span> <code><?php echo realpath(EESELF); ?></code><br>
    <span class="label">BASEPATH:</span> <code><?php echo realpath(BASEPATH); ?></code><br>
    <span class="label">PATH_THEMES:</span> <code><?php echo realpath(PATH_THEMES); ?></code><br>
    <span class="label">PATH_THIRD:</span> <code><?php echo realpath(PATH_THIRD); ?></code><br>
    <span class="label">PATH_CACHE:</span> <code><?php echo realpath(PATH_CACHE); ?></code><br>
    <span class="label">PATH_MBR_THEMES:</span> <code><?php echo realpath(PATH_MBR_THEMES); ?></code>
    <br>
    <?php if (defined('PATH_JAVASCRIPT')): ?>
        <span class="label">PATH_JAVASCRIPT:</span> <code><?php echo realpath(PATH_JAVASCRIPT); ?></code>
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
