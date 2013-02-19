
<link rel="stylesheet" type="text/css" href="<?php echo $theme_css_url."ee_debug_toolbar.css" ?>">

<div id="EEDebug_debug">
	<?php $this->load->view('partials/copyright'); ?>
	<?php $this->load->view('partials/variables'); ?>
	<?php $this->load->view('partials/files'); ?>
	<?php $this->load->view('partials/memory'); ?>
	<?php $this->load->view('partials/time'); ?>
	<?php $this->load->view('partials/config'); ?>
	<?php $this->load->view('partials/db'); ?>
	
	<div id="EEDebug_info">
		<span class="EEDebug_span clickable" data-target="EEDebug_copyright">
			<img src="<?php echo $theme_img_url."logo.png" ?>"
				 style="vertical-align:middle" alt="<?php echo lang('credits'); ?>"
				 title="<?php echo lang('credits'); ?>">  v<?=APP_VER?> <?php //echo ' - '; echo lang('build'). '&nbsp;'.APP_BUILD;?>
			/<?php echo phpversion(); ?>
		</span>
		<span class="EEDebug_span clickable" data-target="EEDebug_variables">
			<img src="<?php echo $theme_img_url."variables.png" ?>"
				 style="vertical-align:middle" alt="<?php echo lang('variables'); ?>"
				 title="<?php echo lang('variables'); ?>">  <?php echo lang('variables'); ?>
		</span>
		<span class="EEDebug_span clickable" data-target="EEDebug_file">
			<img src="<?php echo $theme_img_url."files.png" ?>"
				 style="vertical-align:middle" alt="<?php echo lang('files'); ?>"
				 title="<?php echo lang('files'); ?>"> <?php echo count(get_included_files()); ?> <?php echo lang('files'); ?>
		</span>

		<?php if ($this->input->get("D", FALSE) != 'cp'): ?>
		<span class="EEDebug_span clickable" data-target="EEDebug_memory">
		<?php else: ?>
		<span class="EEDebug_span clickable" data-target="EEDebug_memory_cp">
		<?php endif; ?>
		<img src="<?php echo $theme_img_url."memory.png" ?>"
			 style="vertical-align:middle" alt="<?php echo lang('memory'); ?>"
			 title="<?php echo lang('memory'); ?>"> <?php echo $memory_usage; ?>
		of <?php echo ini_get('memory_limit'); ?>
		</span>
		<span class="EEDebug_span clickable" data-target="EEDebug_time">
			<img src="<?php echo $theme_img_url."time.png" ?>"
				 style="vertical-align:middle" alt="<?php echo lang('time'); ?>"
				 title="<?php echo lang('time'); ?>"> <?php echo $elapsed_time; ?>s
		</span>
		<span class="EEDebug_span clickable" data-target="EEDebug_registry">
			<img src="<?php echo $theme_img_url."config.png" ?>"
				 style="vertical-align:middle" alt="<?php echo lang('configuration_data'); ?>"
				 title="<?php echo lang('configuration_data'); ?>">  <?php echo lang('config'); ?>
			(<?php echo count($config_data); ?>)
		</span>
		<span class="EEDebug_span clickable" data-target="EEDebug_database">
			<img src="<?php echo $theme_img_url."db.png" ?>"
				 style="vertical-align:middle" alt="<?php echo lang('database'); ?>"
				 title="<?php echo lang('database'); ?>"> <?php echo $query_count; ?> <?php echo lang('in'); ?> <?php echo $query_data['total_time']; ?>
			s
		</span>
		<span class="EEDebug_span EEDebug_last clickable" id="EEDebug_toggler">&#171;</span>
	</div>
</div>

<script type="text/javascript">
	window.EEDebug = {data:{}, config:{}};
	window.EEDebug.config.template_debugging_enabled = <?php if($template_debugging_enabled){ echo "true"; }else{ echo "false";}?>;
	window.EEDebug.data.tmpl_data = <?php echo $template_debugging_chart_json?>;
</script>
<script src="<?php echo $theme_js_url . "ee_debug_toolbar.js" ?>" type="text/javascript"
		charset="utf-8" defer id="EEDebug_debug_script"></script>
