# Debug Toolbar

The complete debugging platform for ExpressionEngine. Adds an unobtrusive interface for debugging output on an [ExpressionEngine](http://expressionengine.com "ExpressionEngine") 7.0 site. Replaces default Profiler and Template Debugger provided with ExpressionEngine.

### Features

In addition to what the ExpressionEngine Profiler offers, the Debug Toolbar also offers:

#### Extensible interface for project debug tools

Create custom extensions to expand on debugging capabilities. 

#### Disable and/or Log Email 

The Toolbar can be configured to override email delivery and log the contents to files for easy debugging and quality control. 

#### Custom Error Handler

Control exactly which PHP errors you want to display and log each and every PHP error within every ExpressionEngine request.

#### View Logs 

The Log Panel (included) allows for reading complete log files from within your ExpressionEngine workflow. 

#### Performance Alerts

The Toolbar can be configured for the thresholds to determine when and which SQL query and/or template parse becomes problematic.

## Installation for ExpressionEngine <= 7.3.15

> Note that this is dependent on a pending [Pull Request](https://github.com/ExpressionEngine/ExpressionEngine/pull/3893) that'll implement the needed hook. For now, and previous versions of ExpressionEngine that don't have the hook, you'll have to modify your installation in a very minor manner. 

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
