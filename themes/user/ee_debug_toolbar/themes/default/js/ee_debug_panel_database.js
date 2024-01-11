/**
 * EE Debug Toolbar Log Viewer JS
 */


(function () {

	jQuery("#Eedt_debug_database_panel").on('click', "#EEDebug_slow_queries", function () {
		jQuery(".EEDebug_slow_query").show();
		jQuery(".EEDebug_normal_queries").hide();
		jQuery(".EEDebug_duplicate_queries").hide();
	});
	
	jQuery("#Eedt_debug_database_panel").on('click', "#EEDebug_all_queries", function () {
		jQuery(".EEDebug_normal_queries").show();
		jQuery(".EEDebug_duplicate_queries").hide();
		jQuery(".EEDebug_slow_query").hide();
	});

	jQuery("#Eedt_debug_database_panel").on('click', "#EEDebug_duplicate_queries", function () {
		jQuery(".EEDebug_duplicate_queries").show();
		jQuery(".EEDebug_normal_queries").hide();
		jQuery(".EEDebug_slow_query").hide();
	});

})();