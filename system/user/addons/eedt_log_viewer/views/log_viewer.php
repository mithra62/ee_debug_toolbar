<?php if(!$log_dir_writable): ?>
	<p><?php echo lang('log_dir_not_writable'); ?></p>
<?php endif; ?>


<?php if(!$logs_enabled): ?>
	<p><?php echo lang('logging_not_enabled'); ?></p>
<?php endif; ?>


<?php if($log_dir_writable && $logs_enabled): ?>
	<h4>Log Viewer</h4>
	<div>
	<?php 
		
		$f = fopen($latest_log, 'r');
		$lineNo = 0;
		//$startLine = 3;
		//$endLine = 6;
		echo '<div>';
		while ($line = fgets($f)) {
			$lineNo++;
				
			if($lineNo != '1')
			{
				echo $line.'<br />';
			}
				
			if($lineNo == '1000')
			{
				break;
			}
		}
		fclose($f);	
	?>
	</div>
<?php endif; ?>
