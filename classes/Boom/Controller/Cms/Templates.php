<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * @package	BoomCMS
 * @category	Controllers
 *
 */
class Boom_Controller_Cms_Templates extends Controller_Cms
{
	protected $_view_directory = 'boom/templates';

	public function before()
	{
		parent::before();

		$this->authorization('manage_templates');
	}

	public function action_index()
	{
		$manager = new Template_Manager;
		$imported = $manager->create_new();

		// Get all the templates which now exist in the database.
		$templates = ORM::factory('Template')
			->order_by('name', 'asc')
			->find_all();

		$this->template = View::factory("$this->_view_directory/index", array(
			'imported'		=>	$imported,		// The IDs of the templates which we've just added.
			'templates'	=>	$templates,		// All the templates which are in the database.
			'filenames'	=>	$manager->get_template_filenames(),
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

		$pages = DB::select('page_versions.title', 'page_urls.location')
			->from('page_versions')
			->join(array(
				DB::select(array(DB::expr('max(id)'), 'id'))
					->from('page_versions')
					->group_by('page_id'),
				'current_version'
			))
			->on('page_versions.id', '=', 'current_version.id')
			->join('page_urls', 'inner')
			->on('page_versions.page_id', '=', 'page_urls.page_id')
			->where('page_versions.template_id', '=', $template_id)
			->where('is_primary', '=', true)
			->where('page_deleted', '=', FALSE)
			->order_by('title', 'asc')
			->execute();

		$this->template = View::factory("$this->_view_directory/pages", array(
			'pages'	=>	$pages,
		));
	}

	public function action_save()
	{
		$post = $this->request->post();
		$template_ids = $post['templates'];

		$errors = array();

		foreach ($template_ids as $template_id)
		{
			try
			{
				$template = ORM::factory('Template', $template_id)
					->values(array(
						'name'		=>	$post["name-$template_id"],
						'filename'		=>	$post["filename-$template_id"],
						'description'	=>	$post["description-$template_id"],
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