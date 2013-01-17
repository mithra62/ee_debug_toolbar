EE Debug Toolbar
====================
Adds an unobtrusive interface for debugging output on an [ExpressionEngine](http://expressionengine.com "ExpressionEngine") 2.0 site. Replaces default Profiler and Template Debugger provided with ExpressionEngine.

Adds ExpressionEngine/CodeIgniter version info, available variables, included files, template debugger, all benchmarks, the config data, and all database queries.

![](http://mithra62.com/images/ee_debug_toolbar_default.png)

Graph:
![](http://mithra62.com/images/ee_debug_toolbar_graph.png)

Installation
====================

Upload to your third\_party directory inside a directory called "ee\_debug\_toolbar" and activate within your ExpressionEngine Control Panel.


Benchmark Instructions
====================
To have your [benchmarks](http://codeigniter.com/user_guide/libraries/benchmark.html "benchmarks") used within the Toolbar you have to follow a couple conventions. 

1. Your benchmark names should end in either the string "_start" or "_end".
2. Ensure the names are logical enoughed to be parsed using: 

	`ucwords(str_replace(array('_', '-'), ' ', $key))`

## Examples #
'debug\_toolbar\_start' and 'debug\_toolbar\_end'


Contributors
====================
EE Debug Toolbar is a collaboration between [Eric Lamb](http://blog.ericlamb.net/ "Eric Lamb") ([mithra62](http://mithra62.com/index "mithra62")) and [Christopher Imrie](https://github.com/ckimrie/ "Christopher Imrie").