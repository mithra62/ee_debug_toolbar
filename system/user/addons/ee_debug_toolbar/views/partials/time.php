<h4><?php echo lang('benchmarks'); ?></h4>
<?php
foreach ($benchmark_data AS $key => $value) {
	$key = ucwords(str_replace(array('_', '-'), ' ', $key));
	echo $key . ': ' . $value . '<br />';
}
?>