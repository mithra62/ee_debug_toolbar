(function(){

	eedt.ready(function($, eedt){
		eedt.loadScript("https://www.google.com/jsapi?callback=EedtMemoryHistoryJSAPIReady");
	});

})();

function EedtMemoryHistoryJSAPIReady(){
	var def = new jQuery.Deferred();

	//Load visualisation lib and fetch data
	google.load("visualization", "1", {
		packages:["corechart"],
		callback: function(){
			var d = def;
			eedt.ajax('Eedt_memory_history_ext', 'fetch_memory_and_sql_usage', {
				'cp': eedt.config('cp') ? 'y' : 'n'
			}).then(function(data){
				d.resolve(data);
			});
		}
	});

	def.then(drawChart);

	//Draw chart
	function drawChart(ajaxData) {
		var series = parseData(ajaxData);


		var data = new google.visualization.DataTable();
		data.addColumn('string', 'URL');
		data.addColumn('number', 'Memory Usage');
		data.addColumn('number', 'SQL Query Count');
		data.addColumn('number', 'Total Execution Time');
		data.addRows(series);


		var options = {
			title: "Memory, Query Count & Execution Time",
			titlePosition: 'out',
			titleTextStyle : {
				color:'#fff'
			},
			legend: 'none',
			backgroundColor:'#032f4f',
			colors: ['#e46c63', '#8be47d'],
			chartArea : {
				top:20,
				left:0,
				width:250,
				height:200
			},
			vAxis : {
				baselineColor: '#0e4a85',
				gridlines : {
					count: 10,
					color:'#0e4a85'
				}
			}
		};

		var chart = new google.visualization.LineChart(document.getElementById('Eedt_memory_history_chart'));
		chart.draw(data, options);

		google.visualization.events.addListener(chart, 'select', function (e) {
			window.location = data.getValue(chart.getSelection()[0].row, 0);
		});
	}

	function parseData(data) {
		var parsedData = [],
			memMax = false,
			sqlMax = false,
			execMax = false;

		//Calculate max so we can normalise
		for(var i = 0; i < data.length; i++){

			data[i].peak_memory = Number(data[i].peak_memory);
			data[i].sql_count = Number(data[i].sql_count);
			data[i].execution_time = Number(data[i].execution_time);

			if(memMax === false) {
				memMax = data[i].peak_memory;
			}
			if(sqlMax === false) {
				sqlMax = data[i].sql_count;
			}
			if(execMax === false) {
				execMax = data[i].execution_time;
			}

			if(data[i].peak_memory > memMax) {
				memMax = data[i].peak_memory;
			}

			if(data[i].sql_count > sqlMax) {
				sqlMax = data[i].sql_count;
			}

			if(data[i].execution_time > execMax) {
				execMax = data[i].execution_time;
			}
		}

		for(var i = 0; i < data.length; i++){
			parsedData.push(
				[
					data[i].url,
					{
						v: (data[i].peak_memory / memMax) + 0.6,
						f: String(data[i].peak_memory) + "MB"
					},
					{
						v: (data[i].sql_count / sqlMax) + 0.3,
						f: String(data[i].sql_count) + " queries"
					},
					{
						v: data[i].execution_time / execMax,
						f: String(data[i].execution_time) + "s"
					}
				]
			);
		}

		return parsedData;
	}
}


