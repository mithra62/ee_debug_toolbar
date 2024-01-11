/**
 * EE Debug Toolbar Log Viewer JS
 */


(function () {

	jQuery("#Eedt_debug_database_panel").on('click', "#EEDebug_slow_queries", function () {

		jQuery(".Eedt_debug_database_panel_container").hide();
		jQuery(".EEDebug_slow_query").show();

		jQuery("#Eedt_debug_database_panel_nav_items a").removeClass("flash");
		jQuery("#EEDebug_slow_queries").addClass("flash");
	});
	
	jQuery("#Eedt_debug_database_panel").on('click', "#EEDebug_all_queries", function () {

		jQuery(".Eedt_debug_database_panel_container").hide();
		jQuery(".EEDebug_normal_queries").show();

		jQuery("#Eedt_debug_database_panel_nav_items a").removeClass("flash");
		jQuery("#EEDebug_all_queries").addClass("flash");
	});

	jQuery("#Eedt_debug_database_panel").on('click', "#EEDebug_duplicate_queries", function () {

		jQuery(".Eedt_debug_database_panel_container").hide();
		jQuery(".EEDebug_duplicate_query").show();

		jQuery("#Eedt_debug_database_panel_nav_items a").removeClass("flash");
		jQuery("#EEDebug_duplicate_queries").addClass("flash");
	});

})();