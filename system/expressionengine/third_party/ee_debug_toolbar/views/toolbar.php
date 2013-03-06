
<link rel="stylesheet" type="text/css" href="<?php echo $theme_css_url."ee_debug_toolbar.css" ?>">

<div id="EEDebug_debug">
	<?php foreach($panels AS $key => $panel): ?>
		<div id="<?php echo $panel->getTarget(); ?>" class="EEDebug_panel height-6x"></div>
	<?php endforeach; ?>

	<div id="EEDebug_info">
		<?php foreach($panels AS $key => $panel): ?>
		<span class="EEDebug_span clickable <?php echo $panel->getPanelCss(); ?>" data-target="<?php echo $panel->getTarget(); ?>"  id="EEDebug_<?php echo $panel->getName(); ?>_btn">
			<img src="<?php echo $panel->getButtonIcon(); ?>" style="vertical-align:middle"
				 alt="<?php echo $panel->getButtonIconAltText(); ?>" title="<?php echo $panel->getButtonlabel(); ?>">
				<?php echo $panel->getButtonLabel(); ?>
		</span>
		<?php endforeach; ?>
		<span class="EEDebug_span EEDebug_last clickable" id="EEDebug_toggler">&#171;</span>
	</div>
</div>

<script type="text/javascript">
	window._eedtConfig = <?php echo json_encode($js_config); ?>
</script>
<script src="<?php echo $theme_js_url . "eedt.js" ?>" type="text/javascript"
		charset="utf-8" defer id="EEDebug_debug_script"></script>