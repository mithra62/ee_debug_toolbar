<div id="chart_div"></div>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(drawChart);

function drawChart() {

	var data = new google.visualization.DataTable();
	data.addColumn('string', 'Memory');
	data.addColumn('number', 'Times');
	data.addColumn({type:'string', role:'tooltip'});
	data.addRows(<?php echo count($template_debugging);?>);

	<?php
	$parts = array(); 
	foreach($template_debugging AS $index => $log)
	{
		echo "var tooltip = '".addslashes(str_replace("\n", "\\n", $log['desc']))."\\nMemory:".$log['memory']."MB\\nTime:".$log['time']."';\n";
		echo "data.setValue($index, 0, '".$log['time']."');\n";
		echo "data.setValue($index, 1, ".$log['memory'].");\n";
		echo "data.setValue($index, 2, tooltip);\n";
	}
	echo implode(',', $parts);
	?>
	
	var formatter = new google.visualization.NumberFormat({suffix: 'MB'});
	formatter.format(data, 1);
	//formatter.format(data, 0);	
							
	var options = {
		title: '<?php echo lang('template_debugging'); ?>',
		backgroundColor: 'none',
		width: 600, 
		height: 220,
		hAxis: {
			slantedText: true
		},
		vAxes:[
			{format: '#MB'}
		],		
		legend: {
			position: 'none'
		},
		tooltip: {
			isHtml: false
		},
		chartArea: {
			width: 620, 
			height: "160",
			top: 10,
			left:50
		}
	};
	
	var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
	chart.draw(data, options);
}
</script>