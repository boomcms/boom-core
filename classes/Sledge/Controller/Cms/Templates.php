<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Controller for doing stuff with templates.
 *
 * @package	Sledge
 * @category	Controllers
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 *
 */
class Sledge_Controller_Cms_Templates extends Sledge_Controller
{
	/**
	* Check that they can manage templates.
	*/
	public function before()
	{	
		parent::before();
		
		if ( ! $this->auth->logged_in('manage_templates'))
		{
			throw new HTTP_Exception_403;
		}
	}
	
	public function action_index()
	{	
		// Look for new templates.
		$imported = array();
		if ($dh = @opendir(APPPATH . "views/" . Sledge::TEMPLATE_DIR))
		{
			while ($file = readdir($dh))
			{
				if (preg_match('/\.php$/D',$file) AND substr($file, 0, 1) != '.')
				{
					$file = str_replace(APPPATH . "views/" . Sledge::TEMPLATE_DIR, "", $file);
					$file = str_replace(".php", "", $file);

					$template = ORM::factory('Template')
						->where('filename', '=', $file)
						->find();
					
					if ( ! $template->loaded())
					{
						$template->name = ucwords(str_replace("_", " ", $file));
						$template->filename = $file;
						$template->visible = TRUE;
						$template->save();
						
						$imported[] = $template->pk();
					}
				}
			}
			closedir($dh);
		}

		$this->template = View::factory('sledge/templates/index', array(
			'imported'		=>	$imported,
			'templates'	=>	ORM::factory('Template')->order_by('name', 'asc')->find_all(),
		));
		
		// Get the filenames of the available templates.
		$filenames = Kohana::list_files("views/" . Sledge::TEMPLATE_DIR);
		
		// Remove the directory from the filenames.
		foreach ($filenames as & $filename)
		{
			$filename = str_replace(APPPATH . "views/" . Sledge::TEMPLATE_DIR, "", $filename);
			$filename = str_replace(".php", "", $filename);
		}
		
		$this->template->set('filenames', $filenames);
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
		
		$pages = DB::select('page_versions.title', 'page_uris.uri')
			->from('pages')
			->join('page_versions', 'inner')
			->on('pages.active_vid', '=', 'page_versions.id')
			->join('page_uris', 'inner')
			->on('pages.id', '=', 'page_uris.page_id')
			->where('page_versions.template_id', '=', $template_id)
			->where('primary_uri', '=', TRUE)
			->where('deleted', '=', FALSE)
			->order_by('title', 'asc')
			->execute();
			
		$this->template = View::factory('sledge/templates/page', array(
			'pages'	=>	$pages,
		));
	}
	
	/**
	 * Batch save all the templates.
	 */
	public function action_save()
	{
		$templates = $this->request->post('templates');
		$errors = array();
		
		// Save changes to template data.
		foreach ($templates as $template)
		{
			$filename = $this->request->post("filename-$template");
			
			// Is the filename valid?
			if (strpos($filename, "..") !== FALSE OR substr($filename, 0, 1) == '/' OR ! Kohana::find_file("views/" . Sledge::TEMPLATE_DIR, $filename))
			{
				$errors[] = "Invalid filename: $filename";
			}
			else
			{
				$t = ORM::factory('Template', $template);
				$t->name = $this->request->post("name-$template");
				$t->filename = $filename;
				$t->description = $this->request->post("description-$template");
				$t->visible = (bool) $this->request->post("visible-$template");

				try
				{
					$t->save();
				}
				catch (ORM_Validation_Exception $e)
				{
					$errors[] = $e->errors('models');
				}
			}
		}
		
		// Remove any delete templates.
		$existing = DB::select('template.id')
			->from('template')
			->execute()
			->as_array();
		
		// Get the IDs from the results array
		$existing = Arr::pluck($existing, 'id');		
					
		// Find the IDs of the templates which are in the database but weren't submitted in the form.
		$deleted = array_diff($existing, $templates);
		
		// Templates are versioned so we have to instantiate an object and call delete().
		foreach ($deleted as $d)
		{
			$t = ORM::factory('Template', $d);
			$t->delete();
		}
	}
}
