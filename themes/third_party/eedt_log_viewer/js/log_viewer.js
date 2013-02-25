/**
 * EE Debug Toolbar Log Viewer JS
 */


(function () {
 
	var url = jQuery("#EEDebug_log_viewer_action_url").attr("value");
	jQuery.ajax({
	    type: 'GET',
	    url: url,
	    data: {
	        LANG: "ENG"
	    },
	    dataType: 'html',
	    success: function (data, textStatus) {
	    	jQuery("#EEDebug_log_viewer_data").html(data); 
	    	jQuery("#EEDebug_log_viewer_data").removeClass('EEDebug-log-loading');
	    },
	    error: function (xhr, err, e) {
	        alert("Error: " + err);
	    }
	});	
		
})();