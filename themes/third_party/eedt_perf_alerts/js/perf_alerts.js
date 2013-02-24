/**
 * EE Debug Toolbar Log Viewer JS
 */


(function () {

	jQuery("#EEDebug_slow_queries").click(function () {
		//EEDebugSlideBar();
		jQuery(".EEDebug_normal_queries").hide();
	});
	
	jQuery("#EEDebug_all_queries").click(function () {
		jQuery(".EEDebug_normal_queries").show();
	});	
	
})();