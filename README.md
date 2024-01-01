Debug Toolbar
====================
Adds an unobtrusive interface for debugging output on an [ExpressionEngine](http://expressionengine.com "ExpressionEngine") 7.0 site. Replaces default Profiler and Template Debugger provided with ExpressionEngine.

> Note that this is dependant on a pending [Pull Request](https://github.com/ExpressionEngine/ExpressionEngine/pull/3893) that'll implement the needed hook. For now, and previous versions of ExpressionEngine that don't have the hook, you'll have to modify your installation in a very minor manner. 

Installation 
=============

For this build, you'll have to manually add the below hook call to ExpressionEngine:

`system/ee/legacy/core/Output.php:276` within the `_display()` method. 

```php
if (ee()->extensions->active_hook('before_response_send_output') === true) {
    $output = ee()->extensions->call('before_response_send_output', $output);
    if (ee()->extensions->end_script === true) {
	return;
    }
}
```
