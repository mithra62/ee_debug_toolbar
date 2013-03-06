(function () {
	var args = arguments,
		toolbar,
		panels,
		config;

	//Ensure config is present, or bail out
	config = window._eedtConfig;
	if (!config) {
		console.error("ExpressionEngine developer toolbar config not defined.");
		return;
	}

	//jQuery loaded? If not, load it and start again when it has finished
	if (!window.jQuery) {
		loadScript('//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js', true, function () {
			jQuery.noConflict();
			args.callee();
		});
		return;
	}


	/**
	 |--------------------------------------------------------------------------
	 | Initialise the Panel
	 |--------------------------------------------------------------------------
	 */
	bindToolbar();
	bindPanels();

	//Panels open by default?
	check = getCookie("EEDebugCollapsed");
	if (check != 1) {
		jQuery("#EEDebug_toggler").html("&#171;");
		toolbar.addClass("toolbar-open");
	} else {
		jQuery("#EEDebug_toggler").html("&#187;");
	}


	//After 300 ms add the animation class so we get sliding animation
	setTimeout(function(){
		toolbar.addClass("animate");
	}, 300);


	//Bind toolbar slide toggle
	jQuery("#EEDebug_toggler").click(function () {
		toggleToolbar();
	});




	/**
	 * Bind to Panel Event
	 *
	 * This method simply routes the event request into the panel itself
	 *
	 * @param {string} panel Panel name
	 * @param {string} event Event name
	 * @param {function} [callback] Callback function
	 * @return {jQuery.Deferred}
	 */
	function onPanelEvent(panelName, event, callback) {
		//Allow binding to toolbar events
		if (panelName === "eedt") {
			return onToolbarEvent(event, callback);
		}

		if (!panels[panelName]) {
			console.error("Panel '" + panelName + "' is not a valid panel name")
		}

		return panels[panelName].on(event, callback);
		;
	}



	/**
	 * Bind to toolbar event
	 *
	 * @param {string} event Event name
	 * @param {function} [callback] Callback function
	 * @return {jquery.Deferred}
	 */
	function onToolbarEvent(event, callback) {
		var tb = toolbar,
			def = new jQuery.Deferred();

		toolbar.on(event, function () {
			if (callback) {
				callback(tb);
			}
			def.resolve(tb);
		})

		return def;
	}



	/**
	 * Bind Toolbar Node
	 */
	function bindToolbar() {
		toolbar = jQuery("#EEDebug_debug");
	}



	/**
	 * Instantiate panels
	 */
	function bindPanels() {
		panels = {};
		for (var i = 0; i < config.panels.length; i++) {
			panels[config.panels[i].name] = new Eedt_panel(config.panels[i]);
		}
	}



	/**
	 * Open Toolbar
	 */
	function openToolbar(){
		document.cookie = "EEDebugCollapsed=0;expires=;path=/";
		jQuery("#EEDebug_toggler").html("&#171;");
		toolbar.addClass("toolbar-open");
	}



	/**
	 * Close toolbar
	 */
	function closeToolbar(){
		document.cookie = "EEDebugCollapsed=1;expires=;path=/";
		jQuery("#EEDebug_toggler").html("&#187;")
		toolbar.removeClass("toolbar-open");

		closeAllPanels();
	}



	/**
	 * Toggle toolbar visibility
	 */
	function toggleToolbar(){
		console.log('click')
		if (toolbar.hasClass("toolbar-open")) {
			closeToolbar();
		}
		else {
			openToolbar();
		}
	}



	/**
	 * Close all Panels
	 */
	function closeAllPanels(){
		jQuery.each(panels, function(i, panel){
			panel.close();
		})
	}



	/**
	 * Get Panel By Name
	 *
	 * @param {string} panelName
	 * @return {Eedt_panel}
	 */
	function getPanel(panelName) {
		if (!panels[panelName]) {
			return undefined;
		}

		return panels[panelName];
	}



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



	function ajax(panelName, methodName, callback){
		var here = this,
			url = config.panel_data_url + this.name,
			def = new jQuery.Deferred();

		jQuery.ajax({
			type:'GET',
			url:url,
			data:{
				LANG:"ENG",
				panel: panelName,
				method: methodName
			},
			dataType:'html',
			success:function (data, textStatus) {
				def.resolve(data);
			},
			error:function (xhr, err, e) {
				console.error("Error encountered while fetching data for '" + panelName+ "::" + methodName + "' panel", xhr, err, e);
				def.reject();
			}
		});

		//Trigger callback if specified
		if(typeof callback === 'function') {
			def.done(function(data){
				callback(data);
			})
		}

		return def;
	}



	/**
	 * Lightweight Script Loader
	 *
	 * Even prepends the correct theme URL location to allow easy script loading
	 *
	 * @param  {string}    name        Script filename
	 * @param  {Boolean}   offsite     Script being loaded is offsite? (Basically enable/disable URL_THIRD_THEMES being prepended)
	 * @param  {Function}  cb          Callback function
	 */
	function loadScript(name, offsite, cb) {

		var def,
			loaded = false,
			url = String(name),
			eed = document.createElement('script'),
			local = offsite === true ? false : true;

		eed.type = 'text/javascript';
		eed.async = true;

		//If local url, we can use this scripts URL to ensure we get a correct base url
		if (local) {
			eed.src = String(config.base_js_url + url);
		}
		else {
			eed.src = url;
		}

		//Listen for script load
		eed.onload = eed.onreadystatechange = function () {
			if ((eed.readyState && eed.readyState !== "complete" && eed.readyState !== "loaded") || loaded) {
				return false;
			}
			eed.onload = eed.onreadystatechange = null;
			loaded = true;

			//Fire callback on script load if supplied
			if (typeof cb === "function") {
				cb();
			}
			if(def) {
				def.resolve();
			}
		};
		document.getElementsByTagName('head')[0].appendChild(eed);

		if(window.jQuery) {
			def = new jQuery.Deferred();
			return def;
		}
	}



	/**
	 * Lightweight CSS Loader
	 *
	 * Even prepends the correct theme URL location to allow easy script loading
	 *
	 * @param  {string}    name        CSS filename
	 * @param  {Boolean}   offsite     CSS being loaded is offsite? (Basically enable/disable URL_THIRD_THEMES being prepended)
	 * @param  {Function}  cb          Callback function
	 * @return {Deferred}
	 */
	function loadCss(name, offsite, cb) {

		var def = new jQuery.Deferred(),
			url = String(name),
			eed = document.createElement('link'),
			local = offsite === true ? false : true;

		eed.type = 'text/css';
		eed.rel = 'stylesheet';

		//If local url, we can use this scripts URL to ensure we get a correct base url
		if (local) {
			eed.href = String(config.base_css_url + url);
		}
		else {
			eed.href = url;
		}

		//Fire callback on script load if supplied
		if (typeof cb === "function") {
			def.done(function () {
				cb();
			})
		}

		//Listen for script load
		eed.onload = eed.onreadystatechange = function () {
			def.resolve()
		};
		document.getElementsByTagName('head')[0].appendChild(eed);

		return def;
	}



	/**
	|--------------------------------------------------------------------------
	| EEDT PANEL
	|--------------------------------------------------------------------------
	*/


	/**
	 * EEDT Panel Class
	 * @param {string} name Panel short name
	 * @constructor
	 */
	function Eedt_panel(panelConfig, toolbar) {
		var here = this,
			panelOpen = false,
			initialLoad = true,
			deferreds = {
				init: new jQuery.Deferred()
			};

		this.name = panelConfig.name;
		this.config = panelConfig;
		this.panelNode = jQuery(document.getElementById("EEDebug_" + panelConfig.name));
		this.buttonNode = jQuery(document.getElementById("EEDebug_" + panelConfig.name + "_btn"));


		/**
		 * Initialise Panel
		 *
		 * This is fired on toolbar button click, after resources have been loaded
		 */
		this.init = function() {
			deferreds.init.resolve(this.panelNode, this);

			initialLoad = false;
			this.open();
		}



		/**
		 * Open Panel
		 */
		this.open = function() {
			if(panelOpen) return;

			eedt.closePanels();
			this.panelNode.addClass("active");
			this.panelNode.trigger("open");
			panelOpen = true;
		}



		/**
		 * Close Panel
		 */
		this.close = function(){
			if(!panelOpen) return;

			this.panelNode.removeClass("active");
			this.panelNode.trigger("close");
			panelOpen = false;
		}



		/**
		 * Toggle Panel
		 */
		this.toggle = function(){
			//Toggle panel open
			if(this.panelNode.hasClass("active")){
				this.close();
			} else {
				this.open();
			}
		}



		/**
		 * Bind panel event
		 *
		 * @param {string} event Event name
		 * @param {function} callback Function callback
		 * @return {jQuery.Deferred}
		 */
		this.on = function (event, callback) {
			var here = this;

			//Init only fires once, so we use a deferred object to ensure any late bound event listeners
			//are always triggered
			if(event === "init") {
				deferreds.init.done(function(){
					if (typeof callback === "function") {
						callback(here.panelNode[0], here);
					}
				});
			} else {
				//All other events can fire multiple times, so use custom events on the panel node
				this.panelNode.on(event, function () {
					if (typeof callback === "function") {
						callback(here.panelNode[0], here);
					}
				});
			}
		}



		/**
		 * Load Panel HTML
		 * @return {jQuery.Deferred}
		 */
		this.loadPanelHtml = function () {
			var here = this,
				url = config.panel_data_url + this.name,
				def = new jQuery.Deferred();

			jQuery.ajax({
				type:'GET',
				url:url,
				data:{
					LANG:"ENG"
				},
				dataType:'html',
				success:function (data, textStatus) {
					here.panelNode.html(data);
				},
				error:function (xhr, err, e) {
					console.error("Error encountered while fetching HTML contents for '" + here.name + "' panel", xhr, err, e);
					here.panelNode.removeClass('EEDebug-loading');
					def.reject();
				}
			});

			return def;
		}



		/**
		 * Load Panel JS
		 * @return {jQuery.Deferred}
		 */
		this.loadPanelJs = function () {
			var da = [],
				def = new jQuery.Deferred();

			for (var i = 0; i < this.config.js.length; i++) {
				da.push(loadScript(this.config.js, true));
			}

			//If no items
			if (this.config.js.length === 0) {
				setTimeout(function () {
					def.resolve();
				}, 20);
			}

			return jQuery.when.apply(jQuery, da);
		}



		/**
		 * Load Panel CSS
		 * @return {jQuery.Deferred}
		 */
		this.loadPanelCss = function () {
			var da = [],
				def = new jQuery.Deferred();

			for (var i = 0; i < this.config.css.length; i++) {
				da.push(loadCss(this.config.css, true));
			}

			//If no items
			if (this.config.css.length === 0) {
				setTimeout(function () {
					def.resolve();
				}, 20);
			}

			return jQuery.when.apply(jQuery, da);
		}



		/**
		 * Setup Panel on button click
		 */
		this.buttonNode.on('click', function () {
			var da = [],
				def = new jQuery.Deferred();

			here.panelNode.addClass('EEDebug-loading');

			if(initialLoad){
				da.push(jQuery.proxy(here.loadPanelHtml(), here));
				da.push(jQuery.proxy(here.loadPanelJs(), here));
				da.push(jQuery.proxy(here.loadPanelCss(), here));
			} else {
				here.toggle();
				return;
			}

			jQuery.when.apply(jQuery, da).then(jQuery.proxy(here.init, here));
		})
	}



	/**
	 * EEDT API
	 *
	 * @type {Object}
	 */
	window.eedt = {
		data: {},
		loadScript:loadScript,
		loadCss:loadCss,
		on:onPanelEvent,
		panel:getPanel,
		ajax: ajax,
		closePanels: closeAllPanels
	};

})();