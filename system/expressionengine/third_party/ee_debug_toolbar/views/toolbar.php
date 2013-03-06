
<link rel="stylesheet" type="text/css" href="<?php echo $theme_css_url."ee_debug_toolbar.css" ?>">

<div id="Eedt_debug_toolbar" class="<?php echo $toolbar_position ?>">
	<?php foreach($panels AS $key => $panel): ?>
		<div id="<?php echo $panel->getTarget(); ?>" class="Eedt_debug_panel"></div>
	<?php endforeach; ?>

	<div id="Eedt_debug_toolbar_buttons">
		<span class="Eedt_debug_toolbar_buttons_wrap">
			<?php foreach($panels AS $key => $panel): ?>
				<span class="Eedt_debug_toolbar_button clickable <?php echo $panel->getPanelCss(); ?>" data-target="<?php echo $panel->getTarget(); ?>"  id="Eedt_debug_<?php echo $panel->getName(); ?>_btn">
					<img src="<?php echo $panel->getButtonIcon(); ?>" style="vertical-align:middle"
						 alt="<?php echo $panel->getButtonIconAltText(); ?>" title="<?php echo $panel->getButtonlabel(); ?>">
						<?php echo $panel->getButtonLabel(); ?>
				</span>
			<?php endforeach; ?>
		</span>
		<span class="Eedt_debug_toolbar_button Eedt_debug_toolbar_button_last clickable" id="Eedt_debug_toolbar_toggle_btn">&#171;</span>
	</div>
</div>

<script type="text/javascript">
	window._eedtConfig = <?php echo json_encode($js_config); ?>
</script>
<script src="<?php echo $theme_js_url . "eedt.js" ?>" type="text/javascript"
		charset="utf-8" defer id="EEDebug_debug_script"></script>