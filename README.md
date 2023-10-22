EE Debug Toolbar
====================
Adds an unobtrusive interface for debugging output on an [ExpressionEngine](http://expressionengine.com "ExpressionEngine") 7.0 site. Replaces default Profiler and Template Debugger provided with ExpressionEngine.

Installation 
=============

For this build, you'll have to manually add the below hook call to ExpressionEngine:

`system/ee/ExpressionEngine/Core/Response.php:96` within the `send()` method. 

```php
if (ee()->extensions->active_hook('response_send_output') === true) {
    $this->body = ee()->extensions->call('response_send_output', $this->body);
}
```

It should look like:

```php
public function send()
{
    if (ee()->extensions->active_hook('response_send_output') === true) {
        $this->body = ee()->extensions->call('response_send_output', $this->body);
    }

    if (! $this->body) {
        foreach ($this->headers as $name => $value) {
            $GLOBALS['OUT']->headers[] = array($name . ': ' . $value, true);
        }

        // smoke and mirrors to support the old style
        return $GLOBALS['OUT']->_display('', $this->status);
    }

    $this->sendHeaders();
    $this->sendBody();
}
```

Contributors
====================
EE Debug Toolbar is a collaboration between [Eric Lamb](http://blog.ericlamb.net/ "Eric Lamb") ([mithra62](http://mithra62.com/index "mithra62")) and [Christopher Imrie](https://github.com/ckimrie/ "Christopher Imrie").
