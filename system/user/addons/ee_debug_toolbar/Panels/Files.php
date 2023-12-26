<?php
namespace Mithra62\DebugToolbar\Panels;

class Files extends AbstractPanel
{
	protected $name = "files";

	public function __construct()
	{
		parent::__construct();
		$this->button_label = count(get_included_files()).' '.lang('files');
	}	
}
