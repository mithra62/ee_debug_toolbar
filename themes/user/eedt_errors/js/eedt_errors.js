(function () {

    jQuery("#Eedt_debug_eedt_errors_panel").on('click', "#EEDebug_errors_clear_errors", function () {
        eedt.ajax('Eedt_errors', 'ClearErrorLog', {}).then(function(data){
            jQuery("#eedt_error_content").hide();
            eedt.closePanels();
        });
    });

})();