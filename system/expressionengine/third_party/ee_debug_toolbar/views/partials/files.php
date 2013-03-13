<h4><?php echo lang('file_information'); ?></h4>
<?php echo (count(get_included_files()) + 1); //faked for included graph file below ?> <?php echo lang('files_included'); ?>
<br>
<h4><?php echo lang('requested_url'); ?></h4>
<?php echo $_SERVER['REQUEST_URI']; ?>
<br>
<h4><?php echo lang('system_paths'); ?></h4>

<span class="label"><?php echo lang('bootstrap_file'); ?>:</span> <code><?php echo realpath($included_file_data['bootstrap_file']); ?></code><br>
<span class="label"><?php echo lang('app'); ?>:</span> <code><?php echo realpath(APPPATH); ?></code><br>
<span class="label"><?php echo lang('themes'); ?>:</span> <code><?php echo realpath(PATH_THEMES); ?></code><br>
<span class="label"><?php echo lang('third_party'); ?>:</span> <code><?php echo realpath(PATH_THIRD); ?></code><br>
<span class="label"><?php echo lang('member_themes'); ?>:</span> <code><?php echo realpath(PATH_MBR_THEMES); ?></code><br>
<?php if (defined('PATH_JAVASCRIPT')): ?>
<span class="label"><?php echo lang('javascript'); ?>:</span> <code><?php echo realpath(PATH_JAVASCRIPT); ?></code><br>
<?php endif; ?>

<?php
foreach ($included_file_data AS $section => $files) {
	if (is_array($files) && count($files) >= '1') {
		echo '<h4>' . lang($section) . ' (' . count($files) . ')</h4><pre>';
		foreach ($files AS $file) {
			echo $file . '<br />';
		}
		echo '</pre>';
	}
}
?>