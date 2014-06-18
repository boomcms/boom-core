<?php

namespace Boom\Template;

use \Boom\Template as Template;
use \Kohana as Kohana;
use \ORM as ORM;

class Manager
{
	protected $_template_filenames;

	public function createNew()
	{
		$imported = array();
		foreach ($this->getTemplateFilenames() as $filename) {
			if ( ! $this->templateExistsWithFilename($filename)) {
				$template = $this->createTemplateWithFilename($filename);
				$imported[] = $template->id;
			}
		}

		return $imported;
	}

	public function createTemplateWithFilename($filename)
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
	public function deleteInvalidTemplates()
	{
		foreach ($this->getInvalidTemplates() as $template) {
			$template->delete();
		}
	}

	public function getAllTemplates()
	{
		$finder = new Template\Finder;

		return $finder
			->setOrderBy('name', 'asc')
			->findAll();
	}

	/**
	 * Gets templates where the filename points to an non-existent file.
	 */
	public function getInvalidTemplates()
	{
		$invalid = array();
		$templates = $this->getAllTemplates();

		foreach ($templates as $template) {
			if ( ! $template->fileExists()) {
				$invalid[] = $template;
			}
		}

		return $invalid;
	}

	public function getTemplateFilenames()
	{
		if ( ! $this->_template_filenames) {
			$this->_template_filenames = Kohana::list_files("views/" . \Boom\Template::DIRECTORY);

			foreach ($this->_template_filenames as & $filename) {
				$filename = str_replace(APPPATH . "views/" . \Boom\Template::DIRECTORY, "", $filename);
				$filename = str_replace(EXT, "", $filename);
			}
		}

		return $this->_template_filenames;
	}

	public function getValidTemplates()
	{
		$valid = array();
		$templates = $this->getAllTemplates();

		foreach ($templates as $template) {
			if ($template->fileExists()) {
				$valid[] = $template;
			}
		}

		return $valid;
	}

	public function templateExistsWithFilename($filename)
	{
		$template = new \Boom\Model\Template(array('filename' => $filename));

		return $template->loaded();
	}
}