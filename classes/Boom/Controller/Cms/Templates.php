<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Controller for doing stuff with templates.
 *
 * @package	BoomCMS
 * @category	Controllers
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 *
 */
class Boom_Controller_Cms_Templates extends Boom_Controller
{
	/**
	 *
	 * @var	string	Directory where the views used by this controller are.
	 */
	protected $_view_directory = 'boom/templates';

	/**
	* Check that they can manage templates.
	*/
	public function before()
	{
		parent::before();

		// Are they allowed to view the template manager?
		$this->_authorization('manage_templates');
	}

	/**
	 * Display a list of the CMS templates.
	 * Automagically adds any templates which appear in the filesystem but not in the database.
	 */
	public function action_index()
	{
		// Prepare an array to record imported templates.
		$imported = array();

		// Get the filenames of the available templates.
		// This will be used to import any new templates and to populate a select box in the template manager of template filenames.
		$filenames = Kohana::list_files("views/" . Model_Template::DIRECTORY);

		foreach ($filenames as & $filename)
		{
			// Remove the directory path so that we're just left with a filename.
			$filename = str_replace(APPPATH . "views/" . Model_Template::DIRECTORY, "", $filename);

			// Remove the file extension.
			$filename = str_replace(EXT, "", $filename);
		}

		// Find any templates which don't exist in the database.
		foreach ($filenames as $filename)
		{
			// Does a template with the specified filename already exist?
			// i.e. has the template already been added to the database.
			$template = ORM::factory('Template')
				->where('filename', '=', $filename)
				->find();

			if ( ! $template->loaded())
			{
				// The template doesn't exist so create it.
				$template
					->values(array(
						'name'	=>	ucwords(str_replace("_", " ", $filename)),
						'filename'	=>	$filename,
						'visible'	=>	TRUE,
					))
					->create();

				// Add teh template to the array of imorted templates.
				$imported[] = $template->id;
			}
		}

		// Get all the templates which now exist in the database.
		$templates = ORM::factory('Template')
			->order_by('name', 'asc')
			->find_all();

		$this->template = View::factory("$this->_view_directory/index", array(
			'imported'		=>	$imported,		// The IDs of the templates which we've just added.
			'templates'	=>	$templates,		// All the templates which are in the database.
			'filenames'	=>	$filenames,		// The filenames of all templates on the filesystem.
		));
	}

	/**
	 * Display a list of pages which use a given template.
	 * A template ID is given via the URL.
	 *
	 * @example	/cms/templates/pages/1
	 */
	public function action_pages()
	{
		$template_id = $this->request->param('id');

		$pages = DB::select('page_versions.title', 'page_links.location')
			->from('page_versions')
			->join(array(
				DB::select(array(DB::expr('max(id)'), 'id'))
					->from('page_versions')
					->group_by('page_id'),
				'current_version'
			))
			->on('page_versions.id', '=', 'current_version.id')
			->join('page_links', 'inner')
			->on('page_versions.page_id', '=', 'page_links.page_id')
			->where('page_versions.template_id', '=', $template_id)
			->where('is_primary', '=', TRUE)
			->where('page_deleted', '=', FALSE)
			->order_by('title', 'asc')
			->execute();

		$this->template = View::factory("$this->_view_directory/pages", array(
			'pages'	=>	$pages,
		));
	}

	/**
	 * Batch save all the templates.
	 */
	public function action_save()
	{
		// Get the POST data.
		$post = $this->request->post();

		// Get the template data from the POST array.
		$template_ids = $post['templates'];

		// Define an array to record any errors as we go along.
		$errors = array();

		// Save changes to template data.
		foreach ($template_ids as $template_id)
		{
			try
			{
				// Update the template.
				$template = ORM::factory('Template', $template_id)
					->values(array(
						'name'		=>	$post["name-$template_id"],
						'filename'		=>	$post["filename-$template_id"],
						'description'	=>	$post["description-$template_id"],
						'visible'		=>	(bool) $post["visible-$template_id"],
					))
					->update();
			}
			catch (ORM_Validation_Exception $e)
			{
				$errors[] = $e->errors('models');
			}
		}

		// Get the ID of all templates in the database.
		// Any of these which weren't in the POST array are being deleted.
		$existing = DB::select('id')
			->from('templates')
			->execute()
			->as_array();

		// Get the IDs from the results array
		$existing = Arr::pluck($existing, 'id');

		// Find the IDs of the templates which are in the database but weren't submitted in the form.
		$removed = array_diff($existing, $template_ids);

		// Delete any removed templates.
		if ( ! empty($removed))
		{
			DB::delete('templates')
				->where('id', 'IN', $removed)
				->execute();
		}
	}
}