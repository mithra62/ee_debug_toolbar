(function () {

    jQuery("#Eedt_debug_variables_panel").on('click', "#EEDebug_get", function () {

        jQuery(".Eedt_debug_variables_panel_container").hide();
        jQuery(".EEDebug_get").show();

        jQuery("#Eedt_debug_variables_panel_nav_items a").removeClass("flash");
        jQuery("#EEDebug_get").addClass("flash");
    });

    jQuery("#Eedt_debug_variables_panel").on('click', "#EEDebug_post", function () {

        jQuery(".Eedt_debug_variables_panel_container").hide();
        jQuery(".EEDebug_post").show();

        jQuery("#Eedt_debug_variables_panel_nav_items a").removeClass("flash");
        jQuery("#EEDebug_post").addClass("flash");
    });

    jQuery("#Eedt_debug_variables_panel").on('click', "#EEDebug_headers", function () {

        jQuery(".Eedt_debug_variables_panel_container").hide();
        jQuery(".EEDebug_headers").show();

        jQuery("#Eedt_debug_variables_panel_nav_items a").removeClass("flash");
        jQuery("#EEDebug_headers").addClass("flash");
    });

    jQuery("#Eedt_debug_variables_panel").on('click', "#EEDebug_cookie", function () {

        jQuery(".Eedt_debug_variables_panel_container").hide();
        jQuery(".EEDebug_cookie").show();

        jQuery("#Eedt_debug_variables_panel_nav_items a").removeClass("flash");
        jQuery("#EEDebug_cookie").addClass("flash");
    });

    jQuery("#Eedt_debug_variables_panel").on('click', "#EEDebug_php_session", function () {

        jQuery(".Eedt_debug_variables_panel_container").hide();
        jQuery(".EEDebug_php_session").show();

        jQuery("#Eedt_debug_variables_panel_nav_items a").removeClass("flash");
        jQuery("#EEDebug_php_session").addClass("flash");
    });

    jQuery("#Eedt_debug_variables_panel").on('click', "#EEDebug_ee_session", function () {

        jQuery(".Eedt_debug_variables_panel_container").hide();
        jQuery(".EEDebug_ee_session").show();

        jQuery("#Eedt_debug_variables_panel_nav_items a").removeClass("flash");
        jQuery("#EEDebug_ee_session").addClass("flash");
    });

})();