<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * mithra62 - EE Debug Toolbar
 *
 * @package        mithra62:EE_debug_toolbar
 * @author         Eric Lamb
 * @copyright      Copyright (c) 2012, mithra62, Eric Lamb.
 * @link           http://mithra62.com/
 * @updated        1.0
 * @filesource     ./system/expressionengine/third_party/ee_debug_toolbar/
 */
$config['name'] = 'EE Debug Toolbar';
$config['class_name'] = 'Ee_debug_toolbar';
$config['description'] = 'Adds an unobtrusive interface for debugging output'; 

$config['mod_url_name'] = strtolower($config['class_name']);
$config['ext_class_name'] = $config['class_name'].'_ext';

$config['version'] = '1.0.1';
$config['docs_url'] = 'https://github.com/mithra62/ee_debug_toolbar/wiki';