<!--EEDT CSS-->
<?php foreach($css as $css_url): ?>
<link rel="stylesheet" type="text/css" href="<?php echo $css_url ?>">
<?php endforeach; ?>

<!--PANELS BEFORE TOOLBAR -->
<?php foreach($panels_before_toolbar AS $key => $panel):
	echo $panel->get_panel_contents();
endforeach; ?>

<!--EE DEBUG TOOLBAR-->
<div id="Eedt_debug_toolbar" class="<?php echo $toolbar_position ?>">

	<!--PANELS IN TOOLBAR-->
	<?php foreach($panels_in_toolbar AS $key => $panel): ?>
		<div id="<?php echo $panel->get_target(); ?>" class="Eedt_debug_panel"></div>
	<?php endforeach; ?>

	<div id="Eedt_debug_toolbar_buttons">
		<span class="Eedt_debug_toolbar_buttons_wrap">
			<?php foreach($panels_in_toolbar AS $key => $panel): ?>
				<?php if(!$panel->show_button()) continue;  ?>
				<span class="Eedt_debug_toolbar_button clickable <?php echo $panel->get_panel_css_class(); ?>" data-target="<?php echo $panel->get_target(); ?>"  id="Eedt_debug_<?php echo $panel->get_name(); ?>_btn">
					<?php if($panel->get_button_icon()): ?>
					<img src="<?php echo $panel->get_button_icon(); ?>" style="vertical-align:middle"
						 alt="<?php echo $panel->get_button_icon_alt_text(); ?>" title="<?php echo $panel->get_button_label(); ?>">
					<?php endif; ?>
					<span id="Eedt_debug_<?php echo $panel->get_name(); ?>_copy">
						<?php echo $panel->get_button_label(); ?>
					</span>
				</span>
			<?php endforeach; ?>
		</span>
		<span class="Eedt_debug_toolbar_button Eedt_debug_toolbar_button_last clickable" id="Eedt_debug_toolbar_toggle_btn">&#171;</span>
	</div>
</div>

<!--PANELS AFTER TOOLBAR-->
<?php foreach($panels_after_toolbar AS $key => $panel):
	echo $panel->get_panel_contents();
endforeach; ?>

<!--EEDT.JS CONFIG-->
<script type="text/javascript">
	window._eedtConfig = <?php echo json_encode($js_config); ?>
</script>

<!--EEDT CSS-->
<?php foreach($js as $js_url): ?>
<script src="<?php echo $js_url ?>" type="text/javascript" charset="utf-8" defer id="EEDebug_debug_script"></script>
<?php endforeach; ?>
