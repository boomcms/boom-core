<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Asset controller
 * Contains methods for adding / replacing an asset etc.
 *
 * The asset manager uses AJAX for adding assets etc. so when this controller doesn't use the standard template for its views.
 *
 * @package	Sledge
 * @category	Assets
 * @category	Controllers
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 */
class Sledge_Controller_Cms_Assets extends Sledge_Controller
{
	/**
	 * Check that they can manage assets.
	 */
	public function before()
	{
		parent::before();

		// The permissions check is currently disabled because uploadify can't authenticate.
		// This is a massive security problem which needs to be fixed before any sites launch.
		// if ( ! $this->auth->logged_in('manage assets'))
		// 	throw new HTTP_Exception_403;
	}

	/**
	 * Delete controller.
	 * Allows deleting multiple assets.
	 *
	 * **Accepted POST parameters:**
	 * Name		|	Type	|	Description
	 * -----------|-----------|---------------
	 * assets	|  array 	|	Array of asset IDs to be deleted.
	 *
	 * @uses Model_Asset::delete()
	 */
	public function action_delete()
	{
		$asset_ids = (array) $this->request->post('assets');
		$asset_ids = array_unique($asset_ids);

		foreach ($asset_ids as $asset_id)
		{
			$asset = ORM::factory('Asset', $asset_id);

			// If the asset isn't marked as rubbish then mark it as so.
			// If it's already marked as rubbish then delete it for real.
			if ($asset->rubbish)
			{
				$asset->delete();
				Sledge::log("Deleted asset $asset->title (ID: $asset->id)");
			}
			else
			{
				$asset->rubbish = TRUE;
				$asset->save();
				Sledge::log("Moved asset $asset->title (ID: $asset->id) to rubbish bin.");
			}
		}
	}

	/**
	 * Download controller.
	 * Allows downloading of assets in zip format.
	 *
	 *  This controller performs two roles:
	 * - If a single asset ID is sent then the asset is downloaded in 'normally'
	 * - If multiple asset IDs are sent then a .zip is created which contains the associates assets.
	 *
	 *
	 * **Accepted GET parameters:**
	 * Name		|	Type	|	Description
	 * -----------|-----------|---------------
	 * assets	|  string 	|	Comma separated list of asset IDs.
	 *
	 * @uses ZipArchive
	 */
	public function action_download()
	{
		$asset_ids = array_unique(explode(",", $this->request->query('assets')));
		$assets = count($asset_ids);

		if ($assets > 1)
		{
			// Get the session ID. This is used in a few places within this if block as it's used in the filename for temp zip file.
			$session_id = Session::instance()->id();

			// Multi-asset download.
			// Create the zip archive.
			$zip = new ZipArchive;
			$zip->open('/tmp/cms_assets_' . $session_id . 'file.zip', ZipArchive::CREATE);
			foreach ($asset_ids as $asset_id) {
				$asset = ORM::factory('Asset', $asset_id);
				if ($asset->loaded()) {
					$zip->addFile(ASSETPATH . $asset->id, $asset->filename);
				}
			}

			$zip->close();

			// Send it to the user's browser.
			$this->response
				->headers(array(
					"Content-type" => "application/zip",
					"Content-Disposition" => "attachment; filename=cms_assets.zip",
					"Pragma" => "no-cache",
					"Expires" => "0"
				))
				->body(readfile('/tmp/cms_assets_' . $session_id . 'file.zip'));

			// Delete the temporary file.
			unlink('/tmp/cms_assets_' . $session_id . 'file.zip');
		}
		elseif ($assets == 1)
		{
			// Download a single asset.
			$asset = ORM::factory('Asset', $asset_ids[0]);
			if ($asset->loaded())
			{
				$this->response
					->headers(array(
						"Content-type" => $asset->get_mime(),
						"Content-Disposition" => "attachment; filename=" . basename($asset->filename),
						"Pragma" => "no-cache",
						"Expires" => "0"
					))
					->body(readfile(ASSETPATH . $asset->id));
			}
		}
	}

	/**
	 * Generates the HTML for the filters section of the asset manager.
	 */
	public function action_filters()
	{
		// Get the names of the people who have uploaded assets
		$uploaders = DB::select('id', 'firstname', 'lastname')
			->from('people')
			->where('id', 'in', DB::select('audit_person')
				->from('asset_versions')
				->group_by('rid')
				->where('rubbish', '=', FALSE)
				->distinct(TRUE)
			)
			->order_by('firstname', 'asc')
			->order_by('lastname', 'asc')
			->execute()
			->as_array();

		// Get the available asset types.
		// Used for the 'uploaded by' filter.
		$types = DB::select('type')
			->distinct(TRUE)
			->from('assets')
			->join('asset_versions', 'inner')
			->on('active_vid', '=', 'asset_versions.id')
			->where('rubbish', '=', FALSE)
			->where('type', '!=', 0)
			->execute()
			->as_array();

		$types = Arr::pluck($types, 'type');
		$types = array_map(array('Sledge_Asset', 'get_type'), $types);
		$types = array_map('ucfirst', $types);

		// Put it all in a view.
		$this->template = View::factory('sledge/assets/filters', array(
			'uploaders'	=>	$uploaders,
			'types'		=>	$types,
		));
	}

	/**
	 * Display the asset manager.
	 *
	 */
	public function action_index()
	{
		$this->template = View::factory('sledge/assets/index')
			->set('content', Request::factory('cms/assets/manager')->execute()->body());
	}

	/**
	 * Display a list of assets matching certain filters.
	 * This is used for the main content of the asset manager.
	 *
	 * **Accepted GET parameters:**
	 * Name		|	Type	|	Description
	 * -----------|-----------|---------------
	 * page  	| int	 	|	The current page to display. Optional, default is 1.
	 * perpage	| int		|	Number of assets to display on each page. Optional, default is 30.
	 * tag		| string	|	A tag to filter assets by. Through the magic of hackery also used to filter assets by filters.
	 * sortby	| string	|	The column to sort results by. Optional, default is title.
	 * order		| string	|	The direction to sort assets in. Option, default is ascending.
	 *
	 */
	public function action_list()
	{
		$query_data = $this->request->query();

		// G
		$page = Arr::get($query_data, 'page', 1);
		$perpage = Arr::get($query_data, 'perpage', 30);
		$tag = ORM::factory('Tag', Arr::get($query_data, 'tag'));

		// Get type and uploaded by filters.
		$uploaded_by = Arr::get($query_data, 'uploaded_by');
		$type = Arr::get($query_data, 'type');

		$sortby = Arr::get($query_data, 'sortby');
		$order = Arr::get($query_data, 'order');

		$query = ORM::factory('Asset')
			->where('deleted', '=', FALSE);

		if ($tag->loaded())
		{
			$query->where('tag', '=', $tag);
		}

		if (($sortby == 'audit_time' OR $sortby == 'title' OR $sortby == 'filesize') AND ($order == 'desc' OR $order == 'asc'))
		{
			$query->order_by($sortby, $order);
		}
		else
		{
			$sortby = 'title';
			$order = 'asc';
			$query->order_by('title', 'asc');
		}

		// Apply an uploaded by filter.
		if ($uploaded_by)
		{
			// Filtering by uploaded by.
			$query->where('audit_person', '=', $uploaded_by);
		}

		// Apply an asset type filter.
		if ($type)
		{
			// Filtering by asset type.
			$query->where('type', '=', constant('Sledge_Asset::' . strtoupper($type)));
		}

		// Filtering by deleted assets?
		$query->where('rubbish', '=', ($this->request->query('rubbish') == 'rubbish'));

		$count = clone $query;
		$size = clone $query;
		$total = $count->count_all();
		$size = $size->size_all();

		$assets = $query
			->limit($perpage)
			->offset(($page - 1) * $perpage)
			->find_all();

		if (count($assets) === 0)
		{
			$this->template = View::factory('sledge/assets/none_found');
		}
		else
		{
			$this->template = View::factory('sledge/assets/list', array(
				'assets'		=>	$assets,
				'tag'			=>	$tag,
				'total_size'	=>	$size,
				'total'		=>	$total,
				'sortby'		=>	$sortby,
				'order'		=>	$order,
			));

			$pages = ceil($total / $perpage);

			if ($pages > 1)
			{
				$url = '#tag/' . $this->request->query('tag');
				$pagination = View::factory('pagination/query', array(
					'current_page'		=>	$page,
					'total_pages'		=>	$pages,
					'base_url'			=>	$url,
					'previous_page'		=>	$page - 1,
					'next_page'		=>	($page == $pages) ? 0 : ($page + 1),
				));

				$this->template->set('pagination', $pagination);
			}
		}
	}

	/**
	 * Display the asset manager.
	 * Used by the CMS assets page (/cms/assets) and for editing asset chunks, slideshows, feature images etc.
	 *
	 * This just makes internal requests for the filters and tags. This allows the possibility of making those requests asynchronous in future.
	 * @link http://techportal.ibuildings.com/2010/11/16/optimising-hmvc-web-applications-for-performance/
	 */
	public function action_manager()
	{
		$this->template = View::factory('sledge/assets/manager', array(
			'filters'	=>	Request::factory('cms/assets/filters')->execute(),
			'tags'	=>	Request::factory('cms/tag/tree')->post(array('type' => 1))->execute(),
		));
	}

	/**
	 * Save an asset.
	 *
	 * @todo This should be in Controller_Cms_Assets
	 */
	public function action_save()
	{
		$asset = ORM::factory('Asset', $this->request->param('id'));
		$asset->title = $this->request->post('title');
		$asset->filename = $this->request->post('filename');
		$asset->description = $this->request->post('description');
		$asset->status = $this->request->post('status');
		$asset->visible_from = strtotime($this->request->post('visible_from'));
		$asset->save();

		$this->redirect('/cms/assets/view/' . $asset->id);
	}

	/**
	 * Add tags to an asset.
	 *
	 * **Accepted POST parameters:**
	 * Name		|	Type	|	Description
	 * -----------|-----------|---------------
	 * tags  	|  array 	|	Array of tag IDs to be removed from the asset.
	 *
	 */
	public function action_tag()
	{
		$asset_ids = array_unique(explode("-", $this->request->param('id')));
		$tags = (array) $this->request->post('tags');

		// Convert tag IDs into tag objects.
		foreach ($tags as & $tag)
		{
			$tag = ORM::factory('Tag', $tag);
		}

		foreach ($asset_ids as $asset_id)
		{
			$asset = ORM::factory('Asset', $asset_id);

			if ($asset->loaded())
			{
				foreach ($tags as $tag)
				{
					// Catch exceptions incase the tag has already been applied to this asset.
					try
					{
						$asset->apply_tag($tag);
					}
					catch (Exception $e) {}
				}
			}
		}
	}

	/**
	 * Remove tags from an asset.
	 *
	 * **Accepted POST parameters:**
	 * Name		|	Type	|	Description
	 * -----------|-----------|---------------
	 * tags  	|  array 	|	Array of tag IDs to be removed from the asset.
	 *
	 */
	public function action_untag()
	{
		$asset = ORM::factory('Asset', $this->request->param('id'));

		if ($asset->loaded())
		{
			$tags = (array) $this->request->post('tags');

			DB::delete('tags_applied')
				->where('object_type', '=', $asset->get_object_type_id())
				->where('object_id', '=', $asset->pk())
				->where('tag_id', 'IN', $tags)
				->execute();
		}
	}

	/**
	 * Controller to show an asset's detailed view.
	 * Multiple asset IDs can be given by separating them with a hyphen.
	 *
	 * @example http://site.com/cms/assets/view/1-2-3
	 *
	 */
	public function action_view()
	{
		$asset_ids = (array) explode("-", $this->request->param('id'));
		$assets = array();

		// Get the assets from the database.
		foreach ($asset_ids as $asset_id)
		{
			$asset = ORM::factory('Asset', $asset_id);

			// Don't include assets which don't exist.
			if ($asset->loaded())
			{
				// If the asset is a BOTR video which isn't marked as encoded then attempt to update the information.
				if ($asset->type == Sledge_Asset::BOTR AND ! $asset->encoded)
				{
					Request::factory('cms/video/sync/' . $asset->id)->execute();
					$asset->reload();
				}

				$assets[] = $asset;
			}
		}

		$this->template = View::factory('sledge/assets/detailview', array(
			'assets'	=>	$assets,
		));
	}

}
