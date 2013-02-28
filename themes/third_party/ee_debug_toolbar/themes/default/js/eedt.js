(function (window) {




	/**
	 * Lightweight Script Loader
	 *
	 * Even prepends the correct theme URL location to allow easy script loading
	 *
	 * @param  {string}    name        Script filename
	 * @param  {Boolean}   offsite     Script being loaded is offsite? (Basically enable/disable URL_THIRD_THEMES being prepended)
	 * @param  {Function}  cb          Callback function
	 * @return {Deferred}
	 */
	function loadScript(name, offsite, cb) {

		var def = new $.Deferred(),
			url = String(name),
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

		//Fire callback on script load if supplied
		if (typeof cb === "function") {
			def.done(function(){
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

		var def = new $.Deferred(),
			url = String(name),
			eed = document.createElement('link'),
			local = offsite === true ? false : true;

		eed.type = 'text/css';

		//If local url, we can use this scripts URL to ensure we get a correct base url
		if (local) {
			eed.href = String(document.getElementById("EEDebug_debug_script").src).replace("js/ee_debug_toolbar.js", "css/" + url);
		}
		else {
			eed.href = url;
		}

		//Fire callback on script load if supplied
		if (typeof cb === "function") {
			def.done(function(){
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
	 * EEDT API
	 *
	 * @type {Object}
	 */
	window.eedt = {
		loadScript : loadScript,
		loadCss : loadCss
	};

})(window);