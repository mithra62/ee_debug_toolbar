(function () {
	var args = arguments,
		toolbar,
		readyDeferred,
		readyQueue = window._eedtConfig.readyQueue || [],
		leftToggleArrow = "&#187;",
		rightToggleArrow = "&#171;",
		toolbarToggleBtn,
		panels,
		config;

	//Ensure config is present, or bail out
	config = window._eedtConfig;
	if (!config) {
		console.error("ExpressionEngine developer toolbar config not defined.");
		return;
	}

	//Expose ready method to allow third party scripts to be notified when eedt.js is ready (but before DOMReady)
	//This is jsut in case jQuery needs to be loaded, in which we end up with async script execution
	window.eedt = {
		ready: preDeferredReady
	}

	//jQuery loaded? If not, load it and start again when it has finished
	if (!window.jQuery || !minimumJQueryVersionPresent()) {
		loadScript('//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js', function () {
			//Preserve eedt.ready() callbacks for next method call
			window._eedtConfig.readyQueue = readyQueue;
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
	bindButtons();
	prepOnReadyQueue();

	if (toolbar.hasClass("right")) {
		rightToggleArrow = "&#187;";
		leftToggleArrow = "&#171;";
	}

	//Panels open by default?
	check = getCookie("EEDebugCollapsed");
	if (check != 1) {
		toolbarToggleBtn.html(rightToggleArrow);
		toolbar.addClass("toolbar-open");
	} else {
		toolbarToggleBtn.html(leftToggleArrow);
	}


	//Bind toolbar slide toggle
	toolbarToggleBtn.click(function () {
		toggleToolbar();
	});


	//And lastly show the toolbar!
	toolbar.show();


	/**
	 |--------------------------------------------------------------------------
	 | Methods
	 |--------------------------------------------------------------------------
	 */

	/**
	 * Register callback for when eedt.js is ready
	 *
	 * This method is intended to be called very early in execution,
	 * way before jQuery is guaranteed to be on the page (hence no Deferred)
	 *
	 * @param cb Callback function
	 */
	function preDeferredReady(cb) {
		if (typeof cb === 'function') {
			readyQueue.push(cb);
		}
	}

	/**
	 * Register a callback for when eedt.js is ready
	 *
	 * This method is exposed via the eedt.ready() API later in the lifecycle
	 * after jQuery.Deferred is guaranteed to be on the page
	 *
	 * @param cb Callback function
	 */
	function postDeferredReady(cb) {
		if (typeof cb === 'function') {
			readyDeferred.done(cb);
		}
	}

	/**
	 * Takes all eedt.ready() calls and registers them against a Deferred object
	 */
	function prepOnReadyQueue() {
		readyDeferred = new jQuery.Deferred();

		for (var i = 0; i < readyQueue.length; i++) {
			readyDeferred.done(readyQueue[i]);
		}
	}


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

		toolbar.bind(event, function () {
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
		toolbar = jQuery("#Eedt_debug_toolbar");
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


	function bindButtons() {
		toolbarToggleBtn = jQuery("#Eedt_debug_toolbar_toggle_btn");
	}


	/**
	 * Open Toolbar
	 */
	function openToolbar() {
		document.cookie = "EEDebugCollapsed=0;expires=;path=/";
		toolbarToggleBtn.html(rightToggleArrow);
		toolbar.addClass("toolbar-open");
	}


	/**
	 * Close toolbar
	 */
	function closeToolbar() {
		document.cookie = "EEDebugCollapsed=1;expires=;path=/";
		toolbarToggleBtn.html(leftToggleArrow)
		toolbar.removeClass("toolbar-open");

		closeAllPanels();
	}


	/**
	 * Toggle toolbar visibility
	 */
	function toggleToolbar() {
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
	function closeAllPanels() {
		jQuery.each(panels, function (i, panel) {
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

	function deferredCollection(deferreds) {
		var def = new jQuery.Deferred(),
			completed = 0,
			total = deferreds.length;

		for (var i = 0; i < total; i++) {
			deferreds[i].done(function () {
				completed++;

				if (completed === total) {
					def.resolve();
				}
			});
		}

		if (total === 0) {
			def.resolve();
		}

		return def;
	}


	/**
	 * Get config item
	 *
	 * @param {string} key Config item name
	 * @return {*}
	 */
	function configuration(key) {
		return config[key];
	}

	/**
	 * Ajax request with Class::method
	 *
	 * @param {string} className PHP Class name
	 * @param {string} methodName PHP Method name
	 * @param {object} [data] Query string arguments (ie: GET variables)
	 * @param {function} [callback] Callback function
	 * @returns {jQuery.Deferred}
	 */
	function ajax() {
		var here = this,
			className = arguments[0],
			methodName = arguments[1],
			data = typeof arguments[2] === 'object' ? arguments[2] : {},
			callback = typeof arguments[3] === 'function' ? arguments[3] : typeof arguments[2] === 'function' ? arguments[2] : undefined,
			url = config.panel_ajax_url + "class=" + className + "&method=" + methodName + "&LANG=ENG",
			def = new jQuery.Deferred();

		jQuery.ajax({
			type: 'GET',
			url: url,
			data: data,
			success: function (data, textStatus) {
				def.resolve(data);
			},
			error: function (xhr, err, e) {
				console.error("Error encountered while fetching data for '" + panelName + "::" + methodName + "' panel", xhr, err, e);
				def.reject();
			}
		});

		//Trigger callback if specified
		if (typeof callback === 'function') {
			def.done(function (data) {
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
	 * @param  {Function}  cb          Callback function
	 */
	function loadScript(name, cb) {

		var def,
			loaded = false,
			url = String(name),
			eed = document.createElement('script');

		eed.type = 'text/javascript';
		eed.async = true;

		//If local url, we can use this scripts URL to ensure we get a correct base url
		if (urlIsLocal(name)) {
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
			if (def) {
				def.resolve();
			}
		};
		document.getElementsByTagName('head')[0].appendChild(eed);

		if (window.jQuery) {
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
	 * @param  {Function}  cb          Callback function
	 * @return {Deferred}
	 */
	function loadCss(name, cb) {

		var def = new jQuery.Deferred(),
			url = String(name),
			eed = document.createElement('link');

		eed.type = 'text/css';
		eed.rel = 'stylesheet';

		//If local url, we can use this scripts URL to ensure we get a correct base url
		if (urlIsLocal(name)) {
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
	 * Detects whether the supplied URL is local or not
	 * @param uri
	 * @return {Boolean}
	 */
	function urlIsLocal(uri) {
		var local = true;

		if (String(uri).match(/^(https?:)?\/\//gi)) {
			local = false;
		}

		return local;
	}


	/**
	 * Compare version of jQuery on the page to the minimum required
	 * @returns {boolean}
	 */
	function minimumJQueryVersionPresent() {
		var minimum = 150,
			current;

		//No jQuery? Heck no...
		if (!window.jQuery) {
			return true;
		}

		current = Number(String(window.jQuery.fn.jquery).split('.').join(''));

		//Invalid parsing of number? Better load jQuery just in case
		if (current === NaN) {
			return false;
		}


		if (current >= minimum) {
			return true;
		}
		return false;
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
		this.panelNode = jQuery(document.getElementById("Eedt_debug_" + panelConfig.name + "_panel"));
		this.buttonNode = jQuery(document.getElementById("Eedt_debug_" + panelConfig.name + "_btn"));


		/**
		 * Initialise Panel
		 *
		 * This is fired on toolbar button click, after resources have been loaded
		 */
		this.init = function () {
			here.loading(false);

			deferreds.init.resolve(this.panelNode, this);

			initialLoad = false;
			this.open();
		}


		/**
		 * Open Panel
		 */
		this.open = function () {
			if (panelOpen) return;

			eedt.closePanels();
			this.panelNode.addClass("active");

			here.loading(false);

			this.panelNode.trigger("open");
			panelOpen = true;
		}


		/**
		 * Close Panel
		 */
		this.close = function () {
			if (!panelOpen) return;

			this.panelNode.removeClass("active");
			this.panelNode.trigger("close");
			panelOpen = false;
		}


		/**
		 * Toggle Panel
		 */
		this.toggle = function () {
			//Toggle panel open
			if (this.panelNode.hasClass("active")) {
				this.close();
			} else {
				this.open();
			}
		}


		/**
		 * Show panel loading indicator
		 * @param showLoading
		 */
		this.loading = function (showLoading) {
			if (showLoading === undefined) {
				showLoading = true;
			}

			if (showLoading) {
				this.panelNode.addClass("Eedt_debug_panel_loading");
			} else {
				this.panelNode.removeClass("Eedt_debug_panel_loading");
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
			if (event === "init") {
				deferreds.init.done(function () {
					if (typeof callback === "function") {
						callback(here.panelNode[0], here);
					}
				});
			} else {
				//All other events can fire multiple times, so use custom events on the panel node
				this.panelNode.bind(event, function () {
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
				def = new jQuery.Deferred();

			jQuery.ajax({
				type: 'GET',
				url: this.config.panel_fetch_url,
				data: {
					LANG: "ENG"
				},
				dataType: 'html',
				success: function (data, textStatus) {
					here.panelNode.html(data);
					def.resolve();
				},
				error: function (xhr, err, e) {
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
				da.push(loadScript(this.config.js));
			}

			//If no items
			if (this.config.js.length === 0) {
				setTimeout(function () {
					def.resolve();
				}, 20);
			}

			return deferredCollection(da);
		}


		/**
		 * Load Panel CSS
		 * @return {jQuery.Deferred}
		 */
		this.loadPanelCss = function () {
			var da = [],
				def = new jQuery.Deferred();

			for (var i = 0; i < this.config.css.length; i++) {
				da.push(loadCss(this.config.css));
			}

			//If no items
			if (this.config.css.length === 0) {
				setTimeout(function () {
					def.resolve();
				}, 20);
			}

			return deferredCollection(da);
		}


		/**
		 * Setup Panel on button click
		 */
		this.buttonNode.bind('click', function () {
			var da = [],
				def = new jQuery.Deferred();

			if (initialLoad) {
				here.loading(true);
				da.push(here.loadPanelHtml());
				da.push(here.loadPanelJs());
				da.push(here.loadPanelCss());
			} else {
				here.toggle();
				return;
			}

			deferredCollection(da).done(jQuery.proxy(here.init, here))
		})
	}


	/**
	 * EEDT API
	 *
	 * @type {Object}
	 */
	window.eedt = {
		data: {},
		loadScript: loadScript,
		loadCss: loadCss,
		on: onPanelEvent,
		panel: getPanel,
		ajax: ajax,
		closePanels: closeAllPanels,
		config: configuration,
		ready: postDeferredReady
	};

	//Resolve eedt deferred object
	readyDeferred.resolve(jQuery, window.eedt);

})();