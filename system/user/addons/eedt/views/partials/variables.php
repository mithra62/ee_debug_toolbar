<div style="float:left">
    <h4><?php echo lang('variables'); ?></h4>
</div>
<div style="float:right" id="Eedt_debug_variables_panel_nav_items">
    <a href="javascript:;" id="EEDebug_get" class="flash">$_GET (<?=count($_GET);?>)</a>
    | <a href="javascript:;" id="EEDebug_post" class="">$_POST (<?=count($_POST);?>)</a>
    | <a href="javascript:;" id="EEDebug_headers" class="">Headers</a>
    | <a href="javascript:;" id="EEDebug_cookie" class="">$_COOKIE</a>
    | <a href="javascript:;" id="EEDebug_php_session" class="">$_SESSION</a>
    | <a href="javascript:;" id="EEDebug_ee_session" class=""><?php echo lang('ee_session'); ?></a>
</div>
<br clear="all">
<br clear="all">


<div class="Eedt_debug_variables_panel_container EEDebug_get">
    <h4>$_GET</h4>

    <?php if(count($_GET) >= 1): ?>
    <table style='width:100%;'>
        <?php foreach($_GET AS $key => $value): ?>
            <tr>
                <td style="width:30%"><?=$key;?></td>
                <td><?=$value;?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php else: ?>
        <?=lang('no_get_vars'); ?>
    <?php endif; ?>

</div>

<div class="Eedt_debug_variables_panel_container EEDebug_post" style="display: none">
    <h4>$_POST</h4>
    <?php if(count($_POST) >= 1): ?>
        <table style='width:100%;'>
            <?php foreach($_POST AS $key => $value): ?>
                <tr>
                    <td style="width:30%"><?=$key;?></td>
                    <td><?=$value;?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <?=lang('no_post_vars'); ?>
    <?php endif; ?>
</div>


<div id="EEDebug_headers" class="Eedt_debug_variables_panel_container EEDebug_headers" style="display: none">
    <h4>Headers</h4>
    <table style='width:100%;'>
        <?php foreach(['HTTP_ACCEPT', 'HTTP_USER_AGENT', 'HTTP_CONNECTION', 'SERVER_PORT', 'SERVER_NAME', 'REMOTE_ADDR', 'SERVER_SOFTWARE', 'HTTP_ACCEPT_LANGUAGE', 'SCRIPT_NAME', 'REQUEST_METHOD', ' HTTP_HOST', 'REMOTE_HOST', 'CONTENT_TYPE', 'SERVER_PROTOCOL', 'QUERY_STRING', 'HTTP_ACCEPT_ENCODING', 'HTTP_X_FORWARDED_FOR'] AS $key => $value): ?>
            <tr>
                <td style="width:30%"><?=$value;?></td>
                <td><?php
                    $val = (isset($_SERVER[$value])) ? $_SERVER[$value] : 'N/A';
                    echo $val;
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<div id="EEDebug_cookie" class="Eedt_debug_variables_panel_container EEDebug_cookie" style="display: none">
    <h4>Registered Cookies</h4>
        <?php
        if(!empty($cookie_data['registered']) && is_array($cookie_data['registered'])):
        foreach($cookie_data['registered'] AS $key => $value): ?>
            <span style="font-weight: bold;"><?=$key;?> </span> <small>(<?=$value['type'] ?>) Lifetime: <?=$value['cookie_lifetime'];?></small>
            <pre><?php
            if(is_array($value['value'])) {
                echo ee('eedt:OutputService')->outputArray($value['value']);
            } else {
                echo $value['value'];
            }
            ?>
            </pre>
            <hr><br />
        <?php endforeach; ?>
        <?php else: ?>
        <?=lang('eedt.no_registered_cookies'); ?>
        <?php endif; ?>

    <br>
    <h4>Unregistered Cookies</h4>
    <table style='width:100%;'>
        <?php
        if(!empty($cookie_data['unregistered']) && is_array($cookie_data['unregistered'])):
            foreach($cookie_data['unregistered'] AS $key => $value): ?>
                <tr>
                    <td style="width:30%">Value: <?=$value['value'];?></td>
                    <td>Lifetime: <?=$value['cookie_lifetime'];?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <?=lang('eedt.no_unregistered_cookies'); ?>
        <?php endif; ?>
    </table>

</div>

<div class="Eedt_debug_variables_panel_container EEDebug_ee_session" style="display: none">
    <h4><?php echo lang('ee_session'); ?></h4>

    <table style='width:100%;'>
        <?php foreach($session_data AS $key => $value): ?>
            <tr>
                <td style="width:30%"><?=$key;?></td>
                <td><?php
                    if(is_array($value)) {
                        echo print_r($value, true);
                    } else {
                        echo $value;
                    }

                    ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<div class="Eedt_debug_variables_panel_container EEDebug_php_session" style="display: none">
    <h4>$_SESSION</h4>
    <pre><?php
        if(isset($_SESSION)) {
            echo ee('eedt:OutputService')->outputArray($_SESSION, 'no_session_vars');
        }
         ?></pre>
</div>