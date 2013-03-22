/**
 * Bind to Panel Init
 */
eedt.on("memory", "init", function(node, panel){
	panel.loading(true);

	if(!eedt.config('template_debugging_enabled')) {
		panel.loading(false);
		return;
	}
	if(!window.google){
		eedt.loadScript("https://www.google.com/jsapi?callback=jsapi_ready");
	} else {
		jsapi_ready();
	}
});



/**
 * Google JSAPI Load
 */
function jsapi_ready() {
	google.load("visualization", "1", {
		packages:["corechart"],
		callback:function () {
			var c = new Eedt_memory_graph(eedt.data.tmpl_data, document.getElementById('EEDebug_graph'));
		}
	});

	/**
	 * EE Debug Chart Class
	 *
	 * Keep it inside this closure to minimise window object pollution
	 *
	 * @param data array
	 * @param node HTMLElement
	 * @constructor
	 */
	function Eedt_memory_graph(data, node) {
		var chart,
			datatable = new google.visualization.DataTable(),
			formatter = new google.visualization.NumberFormat({suffix:'MB'}),
			options = {
				//title:'<?php echo lang('template_debugging'); ?>',
				backgroundColor:'none',
				width:640,
				height:220,
				hAxis:{
					slantedText:true
				},
				vAxes:[
					{format:'#MB'}
				],
				legend:{
					position:'none'
				},
				tooltip:{
					isHtml:false
				},
				chartArea:{
					width:660,
					height:"160",
					top:10,
					left:50
				}
			};

		datatable.addColumn('string', 'Memory');
		datatable.addColumn('number', 'Times');
		datatable.addColumn({type:'string', role:'tooltip'});
		datatable.addRows(data.length);

		for (var i = 0; i < data.length; i++) {
			var tooltip = String(data[i].desc + "\nMemory:" + data[i].memory + "\nTime:" + data[i].time);
			datatable.setValue(i, 0, data[i].time);
			datatable.setValue(i, 1, data[i].memory);
			datatable.setValue(i, 2, tooltip);
		}

		formatter.format(datatable, 1);

		chart = new google.visualization.LineChart(node);
		chart.draw(datatable, options);

		//setup the click to list function
		google.visualization.events.addListener(chart, 'select', function() {
			var selection = chart.getSelection();
			var row = "#EEDebug_hash_"+selection[0].row;
			var col = selection[0].column;

			jQuery("#Eedt_debug_memory_panel").addClass("show_template_list").removeClass("show_graph");
			jQuery("#EEDebug_template_list div").removeClass("EEDebug_tmpl_log_active");
			jQuery(row)[0].scrollIntoView();
			jQuery(row).addClass("EEDebug_tmpl_log_active");
		});
		//end click to list

		//Some indicators that the chart is ready

		jQuery("#EEDebug_graph_display").click(function () {
			jQuery("#Eedt_debug_memory_panel").removeClass("show_template_list").addClass("show_graph");
		});

		jQuery("#EEDebug_graph_list").click(function () {
			jQuery("#Eedt_debug_memory_panel").addClass("show_template_list").removeClass("show_graph");
		});

		jQuery("#EEDebug_graph_display").trigger("click");

		eedt.panel("memory").loading(false);
	};
}

