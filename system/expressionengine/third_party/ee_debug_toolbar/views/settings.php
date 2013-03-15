<?php 

$tmpl = array (
		'table_open'          => '<table class="mainTable" border="0" cellspacing="0" cellpadding="0">',

		'row_start'           => '<tr class="even">',
		'row_end'             => '</tr>',
		'cell_start'          => '<td style="width:50%;">',
		'cell_end'            => '</td>',

		'row_alt_start'       => '<tr class="odd">',
		'row_alt_end'         => '</tr>',
		'cell_alt_start'      => '<td>',
		'cell_alt_end'        => '</td>',

		'table_close'         => '</table>'
);

$this->table->set_template($tmpl);
$this->table->set_empty("&nbsp;");
?>
<div class="clear_left shun"></div>

<?php echo form_open($query_base.'settings', array('id'=>'my_accordion'))?>
<input type="hidden" value="yes" name="go_settings" />

<div>
	<?php 
	$this->table->set_heading(lang('settings'),' ');
	$this->table->add_row('<label for="theme">'.lang('theme').'</label><div class="subtext">'.lang('theme_instructions').'</div>', form_dropdown('theme', $available_themes, $settings['theme'], 'id="theme"'. $settings_disable));
	$this->table->add_row('<label for="toolbar_position">'.lang('toolbar_position').'</label><div class="subtext">'.lang('toolbar_position_instructions').'</div>', form_dropdown('toolbar_position', $toolbar_positions, $settings['toolbar_position'], 'id="toolbar_position"'. $settings_disable));
	if ($this->extensions->active_hook('ee_debug_toolbar_settings_form') === TRUE)
	{
		$this->extensions->call('ee_debug_toolbar_settings_form');
	}
		
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