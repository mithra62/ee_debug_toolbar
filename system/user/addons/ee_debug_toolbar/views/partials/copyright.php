<h4><?php echo lang('ee_debug_toolbar_module_name'); ?> v<?php echo $ext_version; ?></h4>

<p><?php echo APP_NAME . ' ' . APP_VER . ' ' . lang('build') . ' (' . APP_BUILD . ')'; ?> <br/>
	CodeIgniter <?php echo ucfirst(lang('version')); ?>: <?php echo CI_VERSION; ?><br />
	<?php echo lang('contributors'); ?> <?php echo lang('contributor_list'); ?></p>

<?php if(file_exists($eedt_theme_path."/config.php")): ?>
<p>
	<?php include $eedt_theme_path."/config.php"; echo $theme_credits['credit_blurb']; ?>

</p>

<?php endif; ?>