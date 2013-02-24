
<link rel="stylesheet" type="text/css" href="<?php echo $theme_css_url."ee_debug_toolbar.css" ?>">

<div id="EEDebug_debug">
	<?php 
	//first let's write out our panels
	foreach($panel_data AS $key => $value)
	{
		if(!empty($value['view_script']))
		{
			$this->load->view($value['view_script']); 
		}
		elseif(!empty($value['view_html']))
		{
			echo $value['view_html'];
		}
	}
	?>
	
	<div id="EEDebug_info">
		<?php foreach($panel_data AS $key => $value): ?>
		<span class="EEDebug_span clickable <?php echo $value['class']; ?>" data-target="<?php echo $value['data_target']; ?>">
			<img src="<?php echo $value['image']; ?>"
				 style="vertical-align:middle" alt="<?php echo $value['title']; ?>"
				 title="<?php echo $value['title']; ?>">  <?php echo $value['title']; ?>
		</span>
		<?php endforeach; ?>
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
<?php echo $extra_html; ?>