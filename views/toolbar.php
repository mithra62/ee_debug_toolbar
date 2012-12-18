<style type="text/css" media="screen">
#EEDebug_debug { font: 11px/1.4em Lucida Grande, Lucida Sans Unicode, sans-serif; position:fixed; bottom:5px; left:5px; color:#000; z-index: 100255;}
#EEDebug_debug ol {margin:10px 0px; padding:0 25px}
#EEDebug_debug li {margin:0 0 10px 0;}
#EEDebug_debug h4 {margin:0; font-size: 11px; font-weight:bold; }
#EEDebug_debug .clickable {cursor:pointer}
#EEDebug_toggler { font-weight:bold; background:#BFBFBF; }
.EEDebug_span { border: 1px solid #999; border-right:0px; background:#DFDFDF; padding: 5px 5px; }
.EEDebug_last { border: 1px solid #999; }
.EEDebug_panel { text-align:left; position:absolute;bottom:21px;width:600px; max-height:400px; overflow:auto; display:none; background:#E8E8E8; padding:5px; border: 1px solid #999; }
.EEDebug_panel .pre {font: 11px/1.4em Monaco, Lucida Console, monospace; margin:0 0 0 22px}
#EEDebug_exception { border:1px solid #CD0A0A;display: block; }
.EEDebug_graph {background: #fff; border:1px solid #ccc; padding-top: 1px;}
.EEDebug_graph canvas { width: 100%; height:100px;}
</style>
<script type="text/javascript" charset="utf-8">

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
		return jQuery("#EEDebug_debug").css("left", "-"+parseInt(jQuery("#EEDebug_debug").outerWidth()-jQuery("#EEDebug_toggler").outerWidth()+1)+"px");
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
		return jQuery("#EEDebug_debug").animate({left:"-"+parseInt(jQuery("#EEDebug_debug").outerWidth()-jQuery("#EEDebug_toggler").outerWidth()+1)+"px"}, "normal", "swing");
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
		graphOriginX, graphOriginY, graphWidth, graphHeight;
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
    for(var i = 0; i < 4;i++) {
    	ctx.moveTo( inset - 5, inset + ((graphHeight / 4) * i) + 0.5);
    	ctx.lineTo( inset + graphWidth, inset + ((graphHeight / 4) * i) +0.5);
    	ctx.stroke();
    }
    for(var i = 1; i <= 10;i++) {
    	ctx.moveTo( inset + ((graphWidth / 10) * i) + 0.5, inset + graphHeight + 5);
    	ctx.lineTo( inset + ((graphWidth / 10) * i) + 0.5, inset);
    	ctx.stroke();
    }
    

    //Max memory & time indicators
    ctx.fillStyle = timePlotLineColor;
    ctx.font = "10px Helvetica, sans-serif";
    ctx.fillText("Total time: " + String(data.max_time) + "s", inset, 10)

    ctx.fillStyle = memoryPlotLineColor;
    ctx.font = "10px Helvetica, sans-serif";
    ctx.fillText("Total Memory: " + String(data.max_memory) + "MB", inset + 110, 10)

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
    for(var i = 1; i < data.data.time.length; i++) {
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
    for(var i = 1; i < data.data.memory.length; i++) {
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
			"memory" : Number(result[2]),
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
			x : i / (raw_data.length-1), 
		});
		data.memory.push({
			y : raw_data[i].memory / max_memory,
			x : i / (raw_data.length-1), 
		});
	}

	return {
		data : data,
		max_time: max_time,
		max_memory: max_memory
	};
}
</script>

<div id="EEDebug_debug">
	<div id="EEDebug_copyright" class="EEDebug_panel">
		<h4><?php echo lang('ee_debug_toolbar_module_name'); ?> v<?php echo $ext_version; ?></h4>
		<p><?php echo APP_NAME.' '.APP_VER.' '.lang('build'). ' ('.APP_BUILD.')'; ?> <br />
		CodeIgniter Version: <?php echo CI_VERSION; ?></p>
	</div>
	<div id="EEDebug_variables" class="EEDebug_panel">
		<h4><?php echo lang('headers'); ?></h4>
		<div id="ZFDebug_headers">
			<div class="pre">
			<?php 
			foreach(array('HTTP_ACCEPT', 'HTTP_USER_AGENT', 'HTTP_CONNECTION', 'SERVER_PORT', 'SERVER_NAME', 'REMOTE_ADDR', 'SERVER_SOFTWARE', 'HTTP_ACCEPT_LANGUAGE', 'SCRIPT_NAME', 'REQUEST_METHOD',' HTTP_HOST', 'REMOTE_HOST', 'CONTENT_TYPE', 'SERVER_PROTOCOL', 'QUERY_STRING', 'HTTP_ACCEPT_ENCODING', 'HTTP_X_FORWARDED_FOR') as $header)
			{
				$val = (isset($_SERVER[$header])) ? $_SERVER[$header] : '';
				echo $header.' =&gt; '.$val.'<br />';
			}				
			?>			
			</div>
		</div>	
		<h4>$_COOKIE</h4>
		<div id="ZFDebug_cookie">
			<div class="pre">
			<?php 
			if(count($_COOKIE) == '0')
			{
				echo lang('no_cookie_vars');
			}
			else
			{			
				foreach($_COOKIE AS $key => $value)
				{
					echo $key .' =&gt; '. $value.'<br />';
				}
			}	
			?>			
			</div>
		</div>
		<h4>$_GET</h4>
		<div id="ZFDebug_get">
			<div class="pre">
			<?php 
			if(count($_GET) == '0')
			{
				echo lang('no_get_vars');
			}
			else
			{
				foreach($_GET AS $key => $value)
				{
					echo $key .' =&gt; '. $value.'<br />';
				}
			}	
			?>
			</div>
		</div>
		<h4>$_POST</h4>
		<div id="ZFDebug_post">
			<div class="pre">
			<?php 
			if(count($_POST) == '0')
			{
				echo lang('no_post_vars');
			}
			else
			{			
				foreach($_POST AS $key => $value)
				{
					echo $key .' =&gt; '. $value.'<br />';
				}	
			}
			?>
			</div>
		</div>
		<h4><?php echo lang('ee_session'); ?></h4>
		<div id="ZFDebug_ee_session">
			<div class="pre">
			<?php 
			if(count($session_data) == '0')
			{
				echo lang('no_session_vars');
			}
			else
			{			
				foreach($session_data AS $key => $value)
				{
					echo $key .' =&gt; '. $value.'<br />';
				}	
			}
			?>
			</div>
		</div>				
	</div>
	<div id="EEDebug_file" class="EEDebug_panel">
		<h4><?php echo lang('file_information'); ?></h4>
		<?php echo count(get_included_files()); ?> <?php echo lang('files_included'); ?><br>

		<h4><?php echo lang('system_paths'); ?></h4>
		
		<?php echo lang('bootstrap_file'); ?>: <?php echo realpath($included_file_data['bootstrap_file']); ?><br>
		<?php echo lang('app'); ?>: <?php echo realpath(APPPATH); ?><br>
		<?php echo lang('themes'); ?>: <?php echo realpath(PATH_THEMES); ?><br>
		<?php echo lang('third_party'); ?>: <?php echo realpath(PATH_THIRD); ?><br>
		<?php echo lang('member_themes'); ?>: <?php echo realpath(PATH_MBR_THEMES); ?><br>
		<?php if(defined('PATH_JAVASCRIPT')): ?>
		<?php echo lang('javascript'); ?>: <?php echo realpath(PATH_JAVASCRIPT); ?><br>
		<?php endif; ?>

		<?php 
		foreach($included_file_data AS $section => $files)
		{ 
			if(is_array($files) && count($files) >= '1')
			{
				echo '<h4>'.lang($section).' ('.count($files).')</h4><div class="pre">';
				foreach($files AS $file)
				{
					echo $file.'<br />';
				}
				echo '</div>';
			}
		}
		?>
	</div>
	<div style="display: none;" id="EEDebug_memory" class="EEDebug_panel">
		<?php 
		if($template_debugging && is_array($template_debugging)):
			echo "<h4>".lang('template_debugging')."</h4>"; 
			foreach($template_debugging AS $log)
			{
				echo $log.'<br />';
			}
		
		else: 
		?>
		<h4><?php echo lang('templates_not_enabled'); ?></h4>
		<?php endif; ?>
	</div>
	<div id="EEDebug_time" class="EEDebug_panel">
		<h4><?php echo lang('benchmarks'); ?></h4>
		<?php 
		foreach($benchmark_data AS $key => $value)	
		{
			$key = ucwords(str_replace(array('_', '-'), ' ', $key));
			echo $key.': '.$value.'<br />';
		}
		?>
	</div>
	<div id="EEDebug_registry" class="EEDebug_panel">
		<h4><?php echo lang('configuration'); ?></h4>
		<div class="pre">
			<?php 
			foreach($config_data AS $key => $value)
			{
				if(!is_array($value))
				{
					echo $key .' =&gt; '.$value.' <br />';
					
				} 
				else 
				{
					echo '<div class="pre">'.$key.'=&gt;';
						foreach($value AS $_k => $_v)
						{
							echo $_k.' =&gt; '.print_r($_v, TRUE).' <br />';
						}
					echo '</div>';	
				}
			} 
			?>
 		</div>
 	</div>
 	<div id="EEDebug_database" class="EEDebug_panel">
 		<h4><?php echo lang('database_queries'); ?></h4>
 		<?php echo lang('query_cache_is'); ?> <?php echo ($config_data['enable_db_caching'] == 'y' ? lang('enabled') : lang('disabled')); ?>
 		<h4><?php echo lang('adapter'); ?> 0</h4>
 		<ol>
 			<?php foreach($query_data['queries'] AS $query): ?>
 			<li><strong>[<?php echo $query['time']; ?> s]</strong> <?php echo $query['query']; ?></li>
 			<?php endforeach; ?>
		</ol>
	</div>
	<div id="EEDebug_info">
		<span class="EEDebug_span clickable" onclick="EEDebugPanel('EEDebug_copyright');">
			<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAh9JREFUeNrsk81P03AYx3/9tWu7di2DBWzGljjc3MiyKQ5mMEETNmNMTEwwXI0XjSYe9ezFcNGrmnCEqychaLhwUBNWZEslMml4md0GrHbr3qjMrZ0/D/wFHLz4PT7vz+fJg3W7XXAaQXBK/fsCRFmWAYQQVHM5YGMYUNnZCZUkyX8mGl3kPR4ACQKQLAv2VlaSDkHQea93vaYogLDZgGWagDiphCPDkaqCfVGcVjc377kCgUWz0wGF1VVAO51A+fhphvd61nwMs/439kSYhibAUZeKLBPS3Nwz0sE22vUGwO123h2Pv6/n8wV9d+8xydp1usd51CiVPEPJ5As0lWq224gBOiPqzO0uL082q7UrZz2DPyYiw2+NemN4P5OZMHQ90WzUL0TdAx98vbxUK1fGiqKYMDRNAJYFoL69jaVnZy9qSv7p+NTtpKv1C7akzPP49cQdCseNll4djUXCN+D3bw8cFW0kNhKdRMm35KWlm3ouh+PTodCT2uHhZXdf76P+jfRrUhAWiKFzb+iDwkwfQ+ecDvadSy2+pMbGHyJqCv01/crmP3+/7eCu1hXlLibNzwcRB/ZSIpG2Up9HzfZvn1WrBkh/cAFSZAWnqLKRzUaAaV7DSFJDO2ePhcGUXPoZtAyjB1Ict0VQVBqjKUD0D3yxdL3ZUVUew7ANyPFFwPHHdDi81ikdsNDOdOhYPGUibsi/hUCK2P9fAH8EGAC9LeqyaNkwxQAAAABJRU5ErkJggg==" style="vertical-align:middle" alt="<?php echo lang('credits'); ?>" title="<?php echo lang('credits'); ?>">  v<?=APP_VER?> <?php //echo ' - '; echo lang('build'). '&nbsp;'.APP_BUILD;?>/<?php echo phpversion(); ?>
		</span>
		<span class="EEDebug_span clickable" onclick="EEDebugPanel('EEDebug_variables');">
			<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAQAAAC1+jfqAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAFWSURBVBgZBcE/SFQBAAfg792dppJeEhjZn80MChpqdQ2iscmlscGi1nBPaGkviKKhONSpvSGHcCrBiDDjEhOC0I68sjvf+/V9RQCsLHRu7k0yvtN8MTMPICJieaLVS5IkafVeTkZEFLGy0JndO6vWNGVafPJVh2p8q/lqZl60DpIkaWcpa1nLYtpJkqR1EPVLz+pX4rj47FDbD2NKJ1U+6jTeTRdL/YuNrkLdhhuAZVP6ukqbh7V0TzmtadSEDZXKhhMG7ekZl24jGDLgtwEd6+jbdWAAEY0gKsPO+KPy01+jGgqlUjTK4ZroK/UVKoeOgJ5CpRyq5e2qjhF1laAS8c+Ymk1ZrVXXt2+9+fJBYUwDpZ4RR7Wtf9u9m2tF8Hwi9zJ3/tg5pW2FHVv7eZJHd75TBPD0QuYze7n4Zdv+ch7cfg8UAcDjq7mfwTycew1AEQAAAMB/0x+5JQ3zQMYAAAAASUVORK5CYII=" style="vertical-align:middle" alt="<?php echo lang('variables'); ?>" title="<?php echo lang('variables'); ?>">  <?php echo lang('variables'); ?>
		</span>
		<span class="EEDebug_span clickable" onclick="EEDebugPanel('EEDebug_file');">
			<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAQAAAC1+jfqAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAADPSURBVCjPdZFNCsIwEEZHPYdSz1DaHsMzuPM6RRcewSO4caPQ3sBDKCK02p+08DmZtGkKlQ+GhHm8MBmiFQUU2ng0B7khClTdQqdBiX1Ma1qMgbDlxh0XnJHiit2JNq5HgAo3KEx7BFAM/PMI0CDB2KNvh1gjHZBi8OR448GnAkeNDEDvKZDh2Xl4cBcwtcKXkZdYLJBYwCCFPDRpMEjNyKcDPC4RbXuPiWKkNABPOuNhItegz0pGFkD+y3p0s48DDB43dU7+eLWes3gdn5Y/LD9Y6skuWXcAAAAASUVORK5CYII=" style="vertical-align:middle" alt="<?php echo lang('files'); ?>" title="<?php echo lang('files'); ?>"> <?php echo count(get_included_files()); ?> <?php echo lang('files'); ?>
		</span>
		<span class="EEDebug_span clickable" onclick="EEDebugPanel('EEDebug_memory');">
			<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAGvSURBVDjLpZO7alZREEbXiSdqJJDKYJNCkPBXYq12prHwBezSCpaidnY+graCYO0DpLRTQcR3EFLl8p+9525xgkRIJJApB2bN+gZmqCouU+NZzVef9isyUYeIRD0RTz482xouBBBNHi5u4JlkgUfx+evhxQ2aJRrJ/oFjUWysXeG45cUBy+aoJ90Sj0LGFY6anw2o1y/mK2ZS5pQ50+2XiBbdCvPk+mpw2OM/Bo92IJMhgiGCox+JeNEksIC11eLwvAhlzuAO37+BG9y9x3FTuiWTzhH61QFvdg5AdAZIB3Mw50AKsaRJYlGsX0tymTzf2y1TR9WwbogYY3ZhxR26gBmocrxMuhZNE435FtmSx1tP8QgiHEvj45d3jNlONouAKrjjzWaDv4CkmmNu/Pz9CzVh++Yd2rIz5tTnwdZmAzNymXT9F5AtMFeaTogJYkJfdsaaGpyO4E62pJ0yUCtKQFxo0hAT1JU2CWNOJ5vvP4AIcKeao17c2ljFE8SKEkVdWWxu42GYK9KE4c3O20pzSpyyoCx4v/6ECkCTCqccKorNxR5uSXgQnmQkw2Xf+Q+0iqQ9Ap64TwAAAABJRU5ErkJggg==" style="vertical-align:middle" alt="<?php echo lang('memory'); ?>" title="<?php echo lang('memory'); ?>"> <?php echo $memory_usage; ?> of <?php echo ini_get('memory_limit'); ?>
		</span>
		<span class="EEDebug_span clickable" onclick="EEDebugPanel('EEDebug_time');">
			<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAKrSURBVDjLpdPbT9IBAMXx/qR6qNbWUy89WS5rmVtutbZalwcNgyRLLMyuoomaZpRQCt5yNRELL0TkBSXUTBT5hZSXQPwBAvor/fZGazlb6+G8nIfP0znbgG3/kz+Knsbb+xxNV63DLxVLHzqV0vCrfMluzFmw1OW8ePEwf8+WgM1UXDnapVgLePr5Nj9DJBJGFEN8+TzKqL2RzkenV4yl5ws2BXob1WVeZxXhoB+PP0xzt0Bly0fKTePozV5GphYQPA46as+gU5/K+w2w6Ev2Ol/KpNCigM01R2uPgDcQIRSJEYys4JmNoO/y0tbnY9JlxnA9M15bfHZHCnjzVN4x7TLz6fMSJqsPgLAoMvV1niSQBGIbUP3Ki93t57XhItVXjulTQHf9hfk5/xgGyzQTgQjx7xvE4nG0j3UsiiLR1VVaLN3YpkTuNLgZGzRSq8wQUoD16flkOPSF28/cLCYkwqvrrAGXC1UYWtuRX1PR5RhgTJTI1Q4wKwzwWHk4kQI6a04nQ99mUOlczMYkFhPrBMQoN+7eQ35Nhc01SvA7OEMSFzTv8c/0UXc54xfQcj/bNzNmRmNy0zctMpeEQFSio/cdvqUICz9AiEPb+DLK2gE+2MrR5qXPpoAn6mxdr1GBwz1FiclDcAPCEkTXIboByz8guA75eg8WxxDtFZloZIdNKaDu5rnt9UVHE5POep6Zh7llmsQlLBNLSMTiEm5hGXXDJ6qb3zJiLaIiJy1Zpjy587ch1ahOKJ6XHGGiv5KeQSfFun4ulb/josZOYY0di/0tw9YCquX7KZVnFW46Ze2V4wU1ivRYe1UWI1Y1vgkDvo9PGLIoabp7kIrctJXSS8eKtjyTtuDErrK8jIYHuQf8VbK0RJUsLfEg94BfIztkLMvP3v3XN/5rfgIYvAvmgKE6GAAAAABJRU5ErkJggg==" style="vertical-align:middle" alt="<?php echo lang('time'); ?>" title="<?php echo lang('time'); ?>"> <?php echo $elapsed_time; ?>s
		</span>
		<span class="EEDebug_span clickable" onclick="EEDebugPanel('EEDebug_registry');">
			<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAH2SURBVDjLjZNLTxNRGIaJv6ZNWeBwkZFLQtGAC4l/wKULV+7YILeSYukEUhJoSASVuCI0hpAYDSUQCJBSoAaC0wbBUi4aWphpO52Zlpa+nnOqCAptJ3k3M3me73LOlAAoyZfDqQdOEvyO89/vRcGZ5HeWmySFYdWHVOQN0vE58jrLJMFJ82hewVU4+bMfqdPxP9VBn+A4D88wP59PwFqmsH7UgeTJEMlsTuIyI5uRsDfCMcmtAtoyhVmOu5kkHZuFsiNA3XuEi+QCdhxluL0D/SvpoO+vhIksiItNiPqqyXgfIL403gjfoTsIL70gQBdim3VQvz2FFnwOxf8E8kYF0rIVYqcRM70Vgf/Pe/ohwsutOJdcpBpP4Mek+jPEfbWQVzkG+7tNcNsqt68tkcLZTIzM6YZ21IbolgHq9j1o+z04nKhHRnlH2p6A32LCvFD55fIYr960VHgSSqCFVDJBEeugh+zw2jnpc0/5rthuRMBaioWBqrVrFylXOUpankIi0AjJY0DC3wD9oA9rAnc2bat+n++2UkH8XHaTZfGQlg3QdlsIbIVX4KSPAv+60L+SO/PECmJiI1lYM9SQBR7b3einfn6kEMwEIZd5Q48sQQt1Qv/xFqt2Tp5x3B8sBmYC71h926az6njdUR6hMy8O17wqFqb5Bd2o/0SFzIZrAAAAAElFTkSuQmCC" style="vertical-align:middle" alt="<?php echo lang('configuration_data'); ?>" title="<?php echo lang('configuration_data'); ?>">  <?php echo lang('config'); ?> (<?php echo count($config_data); ?>)
		</span>
		<span class="EEDebug_span clickable" onclick="EEDebugPanel('EEDebug_database');">
			<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAQAAAC1+jfqAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAEYSURBVBgZBcHPio5hGAfg6/2+R980k6wmJgsJ5U/ZOAqbSc2GnXOwUg7BESgLUeIQ1GSjLFnMwsKGGg1qxJRmPM97/1zXFAAAAEADdlfZzr26miup2svnelq7d2aYgt3rebl585wN6+K3I1/9fJe7O/uIePP2SypJkiRJ0vMhr55FLCA3zgIAOK9uQ4MS361ZOSX+OrTvkgINSjS/HIvhjxNNFGgQsbSmabohKDNoUGLohsls6BaiQIMSs2FYmnXdUsygQYmumy3Nhi6igwalDEOJEjPKP7CA2aFNK8Bkyy3fdNCg7r9/fW3jgpVJbDmy5+PB2IYp4MXFelQ7izPrhkPHB+P5/PjhD5gCgCenx+VR/dODEwD+A3T7nqbxwf1HAAAAAElFTkSuQmCC" style="vertical-align:middle" alt="<?php echo lang('database'); ?>" title="<?php echo lang('database'); ?>"> <?php echo $query_count; ?> <?php echo lang('in'); ?> <?php echo $query_data['total_time']; ?>s
		</span>
		<span class="EEDebug_span EEDebug_last clickable" id="EEDebug_toggler" onclick="EEDebugSlideBar()">&#171;</span>
	</div>
</div>