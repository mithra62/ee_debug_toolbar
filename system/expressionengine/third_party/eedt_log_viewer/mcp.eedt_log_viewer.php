<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * mithra62 - EE Debug Toolbar
 *
 * @package        mithra62:EE_debug_toolbar
 * @author         Eric Lamb
 * @copyright      Copyright (c) 2013, mithra62, Eric Lamb.
 * @link           http://mithra62.com/
 * @updated        1.0
 * @filesource     ./system/expressionengine/third_party/eedt_log_viewer/
 */

/**
 * EE Debug Toolbar - Memory History CP Class
 *
 * Control Panel class
 *
 * @package        mithra62:EE_debug_toolbar
 * @author         Eric Lamb
 * @filesource     ./system/expressionengine/third_party/eedt_log_viewer/mcp.eedt_log_viewer.php
 */
class Eedt_log_viewer_mcp 
{	
	/**
	 * The name of the module; used for links and whatnots
	 * @var string
	 */
	private $mod_name = '';
		
	public function __construct()
	{
		$this->EE =& get_instance();
		$path = dirname(realpath(__FILE__));
		include $path.'/config'.EXT;
		$this->class = $config['class_name'];
		$this->version = $config['version'];		
		$this->mod_name = $config['mod_url_name'];
	}
	
	public function index()
	{
		$this->EE->functions->redirect(BASE.AMP.'C=addons_extensions&M=extension_settings&file=ee_debug_toolbar');
	}

	public function settings()
	{
		$this->EE->functions->redirect(BASE.AMP.'C=addons_extensions&M=extension_settings&file=ee_debug_toolbar');
	}
}