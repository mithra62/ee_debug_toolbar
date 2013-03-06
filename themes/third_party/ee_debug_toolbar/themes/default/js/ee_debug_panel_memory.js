/**
 * Bind to Panel Init
 */
eedt.on("memory", "init", function(){
	eedt.loadScript("ee_google_chart.js", false, function () {
		eedt.loadScript("https://www.google.com/jsapi?callback=jsapi_ready", true, null);
	});
});
