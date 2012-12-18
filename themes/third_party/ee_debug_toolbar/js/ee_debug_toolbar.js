/**
 * EE Debug Toolbar JS
 */
if(!window.jQuery)
{
    (function() {
    var eed = document.createElement('script');
    eed.type = 'text/javascript';
    eed.async = true;
    eed.src = '//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js';
    eed.onload = eed.onreadystatechange = function() {
        jQuery.noConflict();
    };
    document.getElementsByTagName('head')[0].appendChild(eed);
    })();

}

var EEDebugLoad = window.onload;
window.onload = function()
{
        
    if (EEDebugLoad)
    {
        EEDebugLoad();
    }

    EEDebugCollapsed();
};

function EEDebugCollapsed()
{
    var check = getCookie("EEDebugCollapsed");
    if (check == 1)
    {
        EEDebugPanel();
        jQuery("#EEDebug_toggler").html("&#187;");
        return jQuery("#EEDebug_debug").css("left", "-"+parseInt(jQuery("#EEDebug_debug").outerWidth()-jQuery("#EEDebug_toggler").outerWidth()+1, 10)+"px");
    }
}

function getCookie(c_name)
{
    var i,x,y,ARRcookies=document.cookie.split(";");

    for (i=0;i<ARRcookies.length;i++)
    {
        x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
        y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
        x=x.replace(/^\s+|\s+$/g,"");
        if (x==c_name)
        {
            return unescape(y);
        }
     }
}

function EEDebugPanel(name)
{
    jQuery(".EEDebug_panel").each(function(i)
    {
        if(jQuery(this).css("display") == "block")
        {
            jQuery(this).slideUp();
        }
        else
        {
            if (jQuery(this).attr("id") == name)
            jQuery(this).slideDown(function() {
                jQuery(this).css({overflow: "auto"});
            });
            else
            jQuery(this).slideUp();
        }
    });

    if(name == "EEDebug_memory" && !window.EEDebugGraphRendered) {
        EEDebugGraph(name);
        window.EEDebugGraphRendered = true;
    }
}

function EEDebugSlideBar()
{
    if (jQuery("#EEDebug_debug").position().left > 0)
    {
        document.cookie = "EEDebugCollapsed=1;expires=;path=/";
        EEDebugPanel();
        jQuery("#EEDebug_toggler").html("&#187;");
        return jQuery("#EEDebug_debug").animate({left:"-"+parseInt(jQuery("#EEDebug_debug").outerWidth()-jQuery("#EEDebug_toggler").outerWidth()+1, 10)+"px"}, "normal", "swing");
    }
    else
    {
        document.cookie = "EEDebugCollapsed=0;expires=;path=/";
        jQuery("#EEDebug_toggler").html("&#171;");
        return jQuery("#EEDebug_debug").animate({left:"5px"}, "normal", "swing");
    }
}

function EEDebugToggleElement(name, whenHidden, whenVisible)
{
    if(jQuery(name).css("display")=="none")
    {
        jQuery(whenVisible).show();
        jQuery(whenHidden).hide();
    }
    else
    {
        jQuery(whenVisible).hide();
        jQuery(whenHidden).show();
    }
    jQuery(name).slideToggle();
}

function EEDebugGraph(nodeName)
{
        
    var //Runtime Vars
        ctx, height, width, data,
        panel = jQuery("#"+nodeName),
        wrapper = jQuery(document.createElement("div")).addClass("EEDebug_graph"),
        canvas = document.createElement("canvas");

    if(!canvas.getContext) {
        //No convas support. We're done here
        return;
    }

    //Build & add to DOM
    wrapper.append(canvas).prependTo(panel);

    //Canvas needs dimension attributes in order to behave properly
    width = wrapper.width();
    height = wrapper.height();
    jQuery(canvas).attr("width", width);
    jQuery(canvas).attr("height", height);

    ctx = canvas.getContext('2d');

    //Fetch data & render
    data = EEDebugRefreshData(nodeName);

    if(data === false) {
        wrapper.remove();
        return;
    }

    EEDebugRefreshGraph(ctx, data);
}

function EEDebugRefreshGraph (ctx, data) {
    var //Config
        inset = 13,
        backgroundColor = "transparent",

        axisColor = "#999",
        axisLineWidth = 0.5,
        
        axisTickColor = "#dedede",
        axisLineTickWidth = 0.5,

        timePlotLineColor = "#142a78",
        timePlotLineWidth = 3.0,

        memoryPlotLineColor = "#b2252e",
        memoryPlotLineWidth = timePlotLineWidth,

        //Runtime Vars
        i, graphOriginX, graphOriginY, graphWidth, graphHeight;
        width = ctx.canvas.width,
        height = ctx.canvas.height;

        

    graphWidth = width - inset * 2;
    graphHeight = height - inset * 2;
    graphOriginX = inset;
    graphOriginY = graphHeight + inset;
    ctx.fillStyle = backgroundColor;
    
    //Background
    ctx.fillRect (0, 0, width, height);

    /**
     * Axes
     */
    ctx.lineWidth = axisLineWidth;
    ctx.strokeStyle = axisColor;

    ctx.beginPath();
    ctx.moveTo(inset, inset);
    ctx.lineTo(inset, inset + graphHeight);
    ctx.lineTo(graphWidth + inset, graphHeight + inset);
    ctx.stroke();

    //Axis ticks
    ctx.lineWidth = axisLineTickWidth;
    ctx.strokeStyle = axisTickColor;
    for(i = 0; i < 4;i++) {
        ctx.moveTo( inset, inset + ((graphHeight / 4) * i) + 0.5);
        ctx.lineTo( inset + graphWidth, inset + ((graphHeight / 4) * i) +0.5);
        ctx.stroke();
    }
    for(i = 1; i <= 10;i++) {
        ctx.moveTo( inset + ((graphWidth / 10) * i) + 0.5, inset + graphHeight);
        ctx.lineTo( inset + ((graphWidth / 10) * i) + 0.5, inset);
        ctx.stroke();
    }
    

    //Max memory & time indicators
    ctx.fillStyle = timePlotLineColor;
    ctx.font = "10px Helvetica, sans-serif";
    ctx.fillText("Total time: " + String(data.max_time) + "s", inset, 10);

    ctx.fillStyle = memoryPlotLineColor;
    ctx.font = "10px Helvetica, sans-serif";
    ctx.fillText("Total Memory: " + String(data.max_memory) + "MB", inset + 110, 10);

    /**
     * Data Plot
     */

    //Time
    ctx.lineWidth = timePlotLineWidth;
    ctx.strokeStyle = timePlotLineColor;
    ctx.beginPath();
    //First point
    ctx.moveTo((data.data.time[0].x * graphWidth) + inset,  graphHeight - (data.data.time[0].y * graphHeight) + inset);
    //Loop thorugh the rest
    for(i = 1; i < data.data.time.length; i++) {
        ctx.lineTo((data.data.time[i].x * graphWidth) + inset,  graphHeight - (data.data.time[i].y * graphHeight) + inset);
    }

    ctx.stroke();

    //Memory
    ctx.lineWidth = memoryPlotLineWidth;
    ctx.strokeStyle = memoryPlotLineColor;
    ctx.beginPath();
    //First point
    ctx.moveTo((data.data.memory[0].x * graphWidth) + inset,  graphHeight - (data.data.memory[0].y * graphHeight) + inset);
    //Loop thorugh the rest
    for(i = 1; i < data.data.memory.length; i++) {
        ctx.lineTo((data.data.memory[i].x * graphWidth) + inset,  graphHeight - (data.data.memory[i].y * graphHeight) + inset);
    }

    ctx.stroke();
    
}

function EEDebugRefreshData (name) {
    var //Config
        regex = new RegExp(/\((\d+\.\d+)\s+\/\s+(\d+\.\d+)\w{2}\)/gi),

        //Runtime
        max_time, max_memory,
        raw_data = [],
        data = { memory : [], time : []},
        panel = jQuery("#" + name),
        html_string = String(panel.html());

    //Parse debug HTML into useful numerical data
    while((result = regex.exec(html_string)) !== null) {
        raw_data.push({
            "time" : Number(result[1]),
            "memory" : Number(result[2])
        });
    }

    if(raw_data.length === 0) {
        return false;
    }

    //Normalise results as x & y objects that range from 0 to 1
    max_time = raw_data[raw_data.length - 1].time;
    max_memory = raw_data[raw_data.length - 1].memory;
    for(var i = 0; i < raw_data.length; i++) {
        data.time.push({
            y : raw_data[i].time / max_time,
            x : i / (raw_data.length-1)
        });
        data.memory.push({
            y : raw_data[i].memory / max_memory,
            x : i / (raw_data.length-1)
        });
    }

    return {
        data : data,
        max_time: max_time,
        max_memory: max_memory
    };
}