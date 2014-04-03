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

	/**
	 * Deletes templates where the filename points to an non-existent file.
	 */
	public function delete_invalid_templates()
	{
		foreach ($this->get_invalid_templates() as $template)
		{
			$template->delete();
		}
	}

	/**
	 * Gets templates where the filename points to an non-existent file.
	 */
	public function get_invalid_templates()
	{
		$invalid = array();
		$templates = ORM::factory('Template')->order_by('name', 'asc')->find_all();

		foreach ($templates as $template)
		{
			if ( ! $template->file_exists())
			{
				$invalid[] = $template;
			}
		}

		return $invalid;
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

	public function get_valid_templates()
	{
		$valid = array();
		$templates = ORM::factory('Template')->order_by('name', 'asc')->find_all();

		foreach ($templates as $template)
		{
			if ($template->file_exists())
			{
				$valid[] = $template;
			}
		}

		return $valid;
	}

	public function template_exists_with_filename($filename)
	{
		$template = new Model_Template(array('filename' => $filename));

		return $template->loaded();
	}
}