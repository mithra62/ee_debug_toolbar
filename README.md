# Debug Toolbar

The complete debugging platform for ExpressionEngine. Adds an unobtrusive interface for debugging output on an [ExpressionEngine](http://expressionengine.com "ExpressionEngine") 7.0 site. Replaces default Profiler and Template Debugger provided with ExpressionEngine.

### Features

In addition to what the ExpressionEngine Profiler offers, the Debug Toolbar also offers:

#### Extensible interface for project debug tools

Create custom extensions to expand on debugging capabilities. 

#### Disable and/or Log Email 

The Toolbar can be configured to override email delivery, so long as the native ExpressionEngine email service is used, and log the contents to files for easy debugging and quality control. 

#### Custom Error Handler

Control exactly which PHP errors you want to display and log each and every PHP error within every ExpressionEngine request.

#### View Logs 

The Log Panel (included) allows for reading complete log files from within your ExpressionEngine workflow. 

#### Performance Alerts

The Toolbar can be configured for the thresholds to determine when and which SQL query and/or template parse becomes problematic.

#### Debug Mode for Guests

The Toolbar can be configured to display Debug details to any Member Role(s) you want to. Super Admin no longer required.

#### Performance Graph

Get a visual heads up on where exactly performance is an issue on all Front End requests. 

![image](https://github.com/mithra62/ee_debug_toolbar/assets/1008036/415b6748-b1ca-43af-95b5-6543574c7f65)

## Requirements

1. ExpressionEngine >= 7.4
2. PHP >= 7.4
3. Extensions Enabled

## Installation for ExpressionEngine <= 7.3.15 

You'll have to manually add the below hook call to ExpressionEngine:

`system/ee/legacy/core/Output.php:276` within the `_display()` method. 

```php
if (ee()->extensions->active_hook('before_response_send_output') === true) {
    $output = ee()->extensions->call('before_response_send_output', $output);
    if (ee()->extensions->end_script === true) {
	return;
    }
}
```
