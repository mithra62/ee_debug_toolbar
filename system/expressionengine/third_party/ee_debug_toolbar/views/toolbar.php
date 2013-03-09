
<link rel="stylesheet" type="text/css" href="<?php echo $theme_css_url."ee_debug_toolbar.css" ?>">

<div id="Eedt_debug_toolbar" class="<?php echo $toolbar_position ?>">
	<?php foreach($panels AS $key => $panel): ?>
		<div id="<?php echo $panel->get_target(); ?>" class="Eedt_debug_panel"></div>
	<?php endforeach; ?>

	<div id="Eedt_debug_toolbar_buttons">
		<span class="Eedt_debug_toolbar_buttons_wrap">
			<?php foreach($panels AS $key => $panel): ?>
				<span class="Eedt_debug_toolbar_button clickable <?php echo $panel->get_panel_css(); ?>" data-target="<?php echo $panel->get_target(); ?>"  id="Eedt_debug_<?php echo $panel->get_name(); ?>_btn">
					<img src="<?php echo $panel->get_button_icon(); ?>" style="vertical-align:middle"
						 alt="<?php echo $panel->get_button_icon_alt_text(); ?>" title="<?php echo $panel->get_button_label(); ?>">
						<?php echo $panel->get_button_label(); ?>
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