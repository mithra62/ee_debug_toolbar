<?php
namespace DebugToolbar\Panels;

class Files extends AbstractPanel
{
	protected string $name = "files";

	public function __construct()
	{
		parent::__construct();
		$this->button_label = count(get_included_files()).' '.lang('files');
	}	
}
