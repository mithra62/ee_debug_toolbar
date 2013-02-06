/**
 * EE Debug Toolbar JS
 */


(function () {
	//User already opened debug panel?
	var args = arguments,
		check;

	//jQuery loaded? If not, load it and start again when it has finished
	if (!window.jQuery) {
		loadScript('//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js', function () {
			jQuery.noConflict();
			args.callee();
		}, true);

		return;
	}

	//Global object for new script classes & global settings
	window.EEDebug = window.EEDebug || {};


	//Panels open by default?
	check = getCookie("EEDebugCollapsed");
	if (check != 1) {
		EEDebugPanel();
		jQuery("#EEDebug_toggler").html("&#171;");
		jQuery("#EEDebug_debug").addClass("toolbar-open");
	} else {
		jQuery("#EEDebug_toggler").html("&#187;");
	}


	//After 300 ms add the animation class so we get sliding animation
	setTimeout(function(){
		jQuery("#EEDebug_debug").addClass("animate");
	}, 300);


	//Bind panel buttons
	jQuery("#EEDebug_info").find(".clickable").click(function (e) {
		e.preventDefault();
		EEDebugPanel(jQuery(this).data('target'));
		return false;
	});


	//Bind toolbar slide toggle
	jQuery("#EEDebug_toggler").click(function () {
		EEDebugSlideBar();
	});


	/**
	 * Get Cookie
	 *
	 * @param  {string}    c_name Cookie name
	 * @return {string}           Cookie contents
	 */
	function getCookie(c_name) {
		var i, x, y,
			ARRcookies = document.cookie.split(";");

		for (i = 0; i < ARRcookies.length; i++) {
			x = ARRcookies[i].substr(0, ARRcookies[i].indexOf("="));
			y = ARRcookies[i].substr(ARRcookies[i].indexOf("=") + 1);
			x = x.replace(/^\s+|\s+$/g, "");

			if (x == c_name) {
				return unescape(y);
			}
		}
	}


	/**
	 * Open Debug Panel
	 *
	 * @param  {string}    name  Panel name
	 */
	function EEDebugPanel(name) {
		var scriptLoaderProxy = loadScript;

		//Toggle panel open
		if(jQuery(document.getElementById(name)).hasClass("active")){
			jQuery(".EEDebug_panel").removeClass("active");
		} else {
			jQuery(".EEDebug_panel").removeClass("active");
			jQuery(document.getElementById(name)).addClass("active");
		}

		/**
		 * Only initialise the memory chart if:
		 * 	- The EEDebugMemory tab is clicked
		 * 	- The graph has not been rendered yet
		 * 	- Template debugging is enabled
		 */
		if (name == "EEDebug_memory" && !window.EEDebugGraphRendered && window.EEDebug.config.template_debugging_enabled) {

			/**
			 * Load Google Chart Library using Google Loader
			 *
			 * Need to load our JS first so that we have a callback function ready to handle the callback
			 * from the JSAPI loading.
			 */
			jQuery(document.body).addClass("EEDebug-chart-loading");
			loadScript("ee_google_chart.js", function () {
				scriptLoaderProxy("https://www.google.com/jsapi?callback=jsapi_ready", null, true);
			});

			jQuery("#EEDebug_graph_display").click(function () {
				jQuery("#EEDebug_memory").removeClass("show_template_list").addClass("show_graph");
			});

			jQuery("#EEDebug_graph_list").click(function () {
				jQuery("#EEDebug_memory").addClass("show_template_list").removeClass("show_graph");
			});

		}
	}


	/**
	 * Slide toolbar into/out of view
	 */
	function EEDebugSlideBar() {
		if (jQuery("#EEDebug_debug").hasClass("toolbar-open")) {
			document.cookie = "EEDebugCollapsed=1;expires=;path=/";
			EEDebugPanel();
			jQuery("#EEDebug_toggler").html("&#187;")
			jQuery("#EEDebug_debug").removeClass("toolbar-open");
		}
		else {
			document.cookie = "EEDebugCollapsed=0;expires=;path=/";
			jQuery("#EEDebug_toggler").html("&#171;");
			jQuery("#EEDebug_debug").addClass("toolbar-open");
		}
	}


	/**
	 * Lightweight Script Loader
	 *
	 * Even prepends the correct theme URL location to allow easy script loading
	 *
	 * @param  {string}    name    Script filename
	 * @param  {Function}  cb      Callback function
	 * @param  {Boolean}    offsite   Script being loaded is offsite? (Basically enable/disable URL_THIRD_THEMES being prepended)
	 * @return {null}
	 */
	function loadScript(name, cb, offsite) {

		var url = String(name),
			eed = document.createElement('script'),
			local = offsite === true ? false : true;

		eed.type = 'text/javascript';
		eed.async = true;

		//If local url, we can use this scripts URL to ensure we get a correct base url
		if (local) {
			eed.src = String(document.getElementById("EEDebug_debug_script").src).replace("ee_debug_toolbar.js", url);
		}
		else {
			eed.src = url;
		}

		eed.onload = eed.onreadystatechange = function () {
			if (typeof cb === "function") {
				cb();
			}
		};
		document.getElementsByTagName('head')[0].appendChild(eed);
	}
})();