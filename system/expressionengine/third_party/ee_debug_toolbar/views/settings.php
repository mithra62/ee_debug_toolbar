<?php 
$this->table->set_empty("&nbsp;");
?>
<div class="clear_left shun"></div>

<?php echo form_open('C=addons_extensions'.AMP.'M=save_extension_settings'.AMP.'file=ee_debug_toolbar', array('id'=>'my_accordion'))?>
<input type="hidden" value="yes" name="go_settings" />

<div>
	<?php 
	$this->table->set_heading(lang('settings'),' ');
	$this->table->add_row('<label for="theme">'.lang('theme').'</label><div class="subtext">'.lang('theme_instructions').'</div>', form_dropdown('theme', $available_themes, $settings['theme'], 'id="theme"'. $settings_disable));
	
	echo $this->table->generate();
	$this->table->clear();	
	?>
</div>

<br />
<div class="tableFooter">
	<div class="tableSubmit">
		<?php echo form_submit(array('name' => 'submit', 'value' => lang('submit'), 'class' => 'submit'));?>
	</div>
</div>	
<?php echo form_close()?>