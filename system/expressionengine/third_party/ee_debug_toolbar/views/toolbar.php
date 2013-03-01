
<link rel="stylesheet" type="text/css" href="<?php echo $theme_css_url."ee_debug_toolbar.css" ?>">

<div id="EEDebug_debug">
	<?php foreach($panels AS $key => $panel): ?>
		<?php echo $panel->getAjaxUrl(); ?><br />
		<?php echo $panel->getOutput(); ?>
	<?php endforeach; ?>
	<div id="EEDebug_info">
		<?php foreach($panels AS $key => $panel): ?>
		<span class="EEDebug_span clickable <?php echo $panel->getName(); ?>" data-target="<?php echo $panel->getTarget(); ?>">
			<img src="<?php echo $theme_img_url . $panel->getButtonIcon(); ?>" style="vertical-align:middle"
				 alt="<?php echo $panel->getButtonIconAltText(); ?>" title="<?php echo $panel->getButtonlabel(); ?>">
				<?php echo $panel->getButtonLabel(); ?>
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