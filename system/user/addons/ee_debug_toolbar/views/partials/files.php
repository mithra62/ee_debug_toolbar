<h4><?php echo lang('file_information'); ?></h4>
<?php echo(count(get_included_files()) + 1); //faked for included graph file below ?> <?php echo lang('files_included'); ?>
<br>
<h4><?php echo lang('requested_url'); ?></h4>
<?php echo $_SERVER['REQUEST_URI']; ?>
<br>
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

<?php
foreach ($included_file_data as $section => $files) {
    if (is_array($files) && count($files) >= '1') {
        echo '<h4>' . lang($section) . ' (' . count($files) . ')</h4><pre>';
        foreach ($files as $file) {
            echo $file . '<br />';
        }
        echo '</pre>';
    }
}
?>