<?php

class Boom_Template_Manager
{
	protected $_template_filenames;

	public function create_new()
	{
		$imported = array();
		foreach ($this->get_template_filenames() as $filename)
		{
			if ( ! $this->template_exists_with_filename($filename))
			{
				$template = $this->create_template_with_filename($filename);
				$imported[] = $template->id;
			}
		}

		return $imported;
	}

	public function create_template_with_filename($filename)
	{
		return ORM::factory('Template')
			->values(array(
				'name'	=>	ucwords(str_replace("_", " ", $filename)),
				'filename'	=>	$filename,
			))
			->create();
	}

	public function get_template_filenames()
	{
		if ( ! $this->_template_filenames)
		{
			$this->_template_filenames = Kohana::list_files("views/" . Model_Template::DIRECTORY);

			foreach ($this->_template_filenames as & $filename)
			{
				$filename = str_replace(APPPATH . "views/" . Model_Template::DIRECTORY, "", $filename);
				$filename = str_replace(EXT, "", $filename);
			}
		}

		return $this->_template_filenames;
	}

	public function template_exists_with_filename($filename)
	{
		$template = new Model_Template(array('filename' => $filename));

		return $template->loaded();
	}
}