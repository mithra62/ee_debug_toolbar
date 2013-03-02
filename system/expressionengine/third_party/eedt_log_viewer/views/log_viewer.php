<div id="EEDebug_log_viewer" class="EEDebug_panel">

	<?php if(!$log_dir_writable): ?>
		<p><?php echo lang('log_dir_not_writable'); ?></p>
	<?php endif; ?>
	
	
	<?php if(!$logs_enabled): ?>
		<p><?php echo lang('logging_not_enabled'); ?></p>
	<?php endif; ?>
	
	<?php echo $ajax_action_url; //the URL to use for routing ACT requests through ?>
	
	<?php if($log_dir_writable && $logs_enabled): ?>
		<h4>Log Viewer</h4>
		<div id="EEDebug_log_viewer_data" class="EEDebug-log-loading"> </div>
		<input type="hidden" id="EEDebug_log_viewer_action_url" name="EEDebug_log_viewer_action_url" value="<?php echo $ajax_action_url; ?>" />	
	<?php endif; ?>
</div>