/**
 * EE Debug Toolbar Files JS
 */


(function () {

    jQuery("#Eedt_debug_files_panel").on('click', "#EEDebug_general_files", function () {

        jQuery(".Eedt_debug_files_panel_container").hide();
        jQuery(".EEDebug_general_files").show();

        jQuery("#Eedt_debug_files_panel_nav_items a").removeClass("flash");
        jQuery("#EEDebug_general_files").addClass("flash");
    });

    jQuery("#Eedt_debug_files_panel").on('click', "#EEDebug_addon_files", function () {

        jQuery(".Eedt_debug_files_panel_container").hide();
        jQuery(".EEDebug_addon_files").show();

        jQuery("#Eedt_debug_files_panel_nav_items a").removeClass("flash");
        jQuery("#EEDebug_addon_files").addClass("flash");
    });

    jQuery("#Eedt_debug_files_panel").on('click', "#EEDebug_ee_files", function () {

        jQuery(".Eedt_debug_files_panel_container").hide();
        jQuery(".EEDebug_ee_files").show();

        jQuery("#Eedt_debug_files_panel_nav_items a").removeClass("flash");
        jQuery("#EEDebug_ee_files").addClass("flash");
    });

    jQuery("#Eedt_debug_files_panel").on('click', "#EEDebug_composer_files", function () {

        jQuery(".Eedt_debug_files_panel_container").hide();
        jQuery(".EEDebug_composer_files").show();

        jQuery("#Eedt_debug_files_panel_nav_items a").removeClass("flash");
        jQuery("#EEDebug_composer_files").addClass("flash");
    });

    jQuery("#Eedt_debug_files_panel").on('click', "#EEDebug_other_files", function () {

        jQuery(".Eedt_debug_files_panel_container").hide();
        jQuery(".EEDebug_other_files").show();

        jQuery("#Eedt_debug_files_panel_nav_items a").removeClass("flash");
        jQuery("#EEDebug_other_files").addClass("flash");
    });

})();