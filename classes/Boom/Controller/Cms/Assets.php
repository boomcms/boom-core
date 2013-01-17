<?php defined('SYSPATH') OR die('No direct script access.');

/**
  * Asset controller
  * Contains methods for adding / replacing an asset etc.
  *
  * @package	BoomCMS
  * @category	Assets
  * @category	Controllers
  * @author	Rob Taylor
  * @copyright	Hoop Associates
  */
class Boom_Controller_Cms_Assets extends Boom_Controller
{
	/**
	 *
	 * @var	string	Directory where the view files used in this class are stored.
	 */
	protected $_view_directory = 'boom/assets';

	/**
	 * Check that they can manage assets.
	 */
	public function before()
	{
		parent::before();

		// Permissions check.
		$this->authorization('manage_assets');
	}

	/**
	 * Deletes assets
	 *
	 * @uses	Model_Asset::delete()
	 * @uses	Boom_Controller::log()
	 */
	public function action_delete()
	{
		// Get the asset IDs from the POST data.
		$asset_ids = (array) $this->request->post('assets');

		// Make sure no assets appear in the array multiple times.
		$asset_ids = array_unique($asset_ids);

		foreach ($asset_ids as $asset_id)
		{
			// Load the asset from the database.
			$asset = new Model_Asset($asset_id);

			// Log a different action depending on whether the asset is being completely deleted
			// or just marked as deleted.
			$log_message = ($asset->deleted)? "Deleted asset $asset->title (ID: $asset->id)" : "Moved asset $asset->title (ID: $asset->id) to rubbish bin.";

			// Call [Model_Asset::delete()]
			// If the asset isn't marked as deleted then it will be marked it as so.
			// If it's already marked as deleted then it will be deleted it for real.
			$asset->delete();

			// Log the action.
			$this->log($log_message);
		}
	}

	/**
	 * Download controller.
	 * Allows downloading of assets in zip format.
	 *
	 *  This controller performs two roles:
	 *	*	If a single asset ID is sent then the asset is downloaded  'normally'
	 *	*	If multiple asset IDs are sent then a .zip is created which contains the associates assets.
	 *
	 *
	 * **Accepted GET parameters:**
	 * Name		|	Type		|	Description
	 * ---------------|-----------	------|---------------
	 * assets		|  string		|	Comma separated list of asset IDs.
	 *
	 * @uses ZipArchive
	 */
	public function action_download()
	{
		// Get a unique array of asset IDs to download.
		$asset_ids = array_unique(explode(",", $this->request->query('assets')));

		// How many assets are we downloading?
		$asset_count = count($asset_ids);

		if ($asset_count > 1)
		{
			// Multiple asset download - create a zip file of assets.

			// Get the session ID. This is used in a few places within this if block as it's used in the filename for temp zip file.
			$session_id = Session::instance()
				->id();

			// The name of the temporary file where the zip archive will be created.
			$tmp_filename = APPPATH . 'cache/cms_assets_' . $session_id . 'file.zip';

			// Create the zip archive.
			$zip = new ZipArchive;
			$zip->open($tmp_filename, ZipArchive::CREATE);

			// Add the assets to the zip archive
			foreach ($asset_ids as $asset_id)
			{
				// Load the asset from the database to check that it exists.
				$asset = new Model_Asset($asset_id);

				if ($asset->loaded())
				{
					// Asset exists add it to the archive.
					$zip->addFile(Boom_Asset::$path . $asset->id, $asset->filename);
				}
			}

			// Finished adding files to the archive.
			$zip->close();

			// Send it to the user's browser.
			$this->response
				->headers(array(
					"Content-type" => "application/zip",
					"Content-Disposition" => "attachment; filename=cms_assets.zip",
					"Pragma" => "no-cache",
					"Expires" => "0"
				))
				->body(
					readfile($tmp_filename)
				);

			// Delete the temporary file.
			unlink($tmp_filename);
		}
		elseif ($asset_count == 1)
		{
			// Download a single asset.

			// Load the asset from the database to check that it exists.
			$asset = new Model_Asset($asset_ids[0]);

			if ($asset->loaded())
			{
				// Asset exists, send the file contents.
				$this->response
					->headers(array(
						"Content-type" => $asset->get_mime(),
						"Content-Disposition" => "attachment; filename=" . basename($asset->filename),
						"Pragma" => "no-cache",
						"Expires" => "0"
					))
					->body(
						readfile(Boom_Asset::$path . $asset->id)
					);
			}
		}
	}

	/**
	 * Generates the HTML for the filters section of the asset manager.
	 */
	public function action_filters()
	{
		// Get the names of the people who have uploaded assets
		$uploaders = DB::select('id', 'name')
			->from('people')
			->where('id', 'in', DB::select('uploaded_by')
				->from('assets')
				->where('deleted', '=', FALSE)
				->distinct(TRUE)
			)
			->order_by('name', 'asc')
			->execute()
			->as_array();

		// Get the available asset types.
		// Used for the 'uploaded by' filter.
		$types = DB::select('type')
			->distinct(TRUE)
			->from('assets')
			->where('deleted', '=', FALSE)
			->where('type', '!=', 0)
			->execute()
			->as_array();

		// Turn the numeric asset types into user friendly strings.
		$types = Arr::pluck($types, 'type');
		$types = array_map(array('Boom_Asset', 'type'), $types);
		$types = array_map('ucfirst', $types);

		// Put it all in a view.
		$this->template = View::factory("$this->_view_directory/filters", array(
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
		$this->template = View::factory("$this->_view_directory/index", array(
			'content'	=>	Request::factory('cms/assets/manager')
				->execute()
				->body()
		));
	}

	/**
	 * Display a list of assets matching certain filters.
	 * This is used for the main content of the asset manager.
	 *
	 * **Accepted GET parameters:**
	 * Name		|	Type		|	Description
	 * ---------------|-----------------|---------------
	 * page		|	 int	 	|	The current page to display. Optional, default is 1.
	 * perpage	|	 int		|	Number of assets to display on each page. Optional, default is 30.
	 * tag		|	 string	|	A tag to filter assets by. Through the magic of hackery also used to filter assets by filters.
	 * sortby		|	 string	|	The column to sort results by. Optional, default is title.
	 * order		|	string	|	The direction to sort assets in. Option, default is ascending.
	 *
	 */
	public function action_list()
	{
		// Get the query data.
		$query_data = $this->request->query();

		// Load the query data into variables.
		$page		=	Arr::get($query_data, 'page', 1);
		$perpage		=	Arr::get($query_data, 'perpage', 30);
		$tag			=	new Model_Tag(Arr::get($query_data, 'tag'));
		$uploaded_by	=	Arr::get($query_data, 'uploaded_by');
		$type		=	Arr::get($query_data, 'type');
		$sortby		=	Arr::get($query_data, 'sortby');
		$order		=	Arr::get($query_data, 'order');
		$title			=	Arr::get($query_data, 'title');

		// Prepare the database query.
		$query = DB::select()
			->from('assets');

		// If a valid tag was given then filter the results by tag..
		if ($tag->loaded())
		{
			$query
				->join('tags_applied', 'inner')
				->on('tags_applied.object_id', '=', 'assets.id')
				->join('tags', 'inner')
				->on('tags_applied.tag_id', '=', 'tags.id')
				->where('tags_applied.object_type', '=', 1)
				->where('tags.path', 'like', $tag->path . '%');
		}

		// Filtering by title?
		if ($title)
		{
			$query->where('title', 'like', "%$title%");
		}

		if (($sortby == 'last_modified' OR $sortby == 'title' OR $sortby == 'filesize') AND ($order == 'desc' OR $order == 'asc'))
		{
			// A valid sort column and direction was given so use them.
			$query->order_by($sortby, $order);
		}
		else
		{
			// No sort column or direction was given, or one of them was invalid, sort by title ascending by default.
			$sortby = 'title';
			$order = 'asc';
			$query->order_by('title', 'asc');
		}

		// Apply an uploaded by filter.
		if ($uploaded_by)
		{
			// Filtering by uploaded by.
			$query->where('uploaded_by', '=', $uploaded_by);
		}

		// Apply an asset type filter.
		if ($type)
		{
			// Filtering by asset type.
			$query->where('type', '=', constant('Boom_Asset::' . strtoupper($type)));
		}

		// Filtering by deleted assets?
		$query->where('deleted', '=', ($this->request->query('rubbish') == 'rubbish'));

		// Clone the query to count the number of matching assets and their total size.
		$query2 = clone $query;
		$result = $query2
			->select(array(DB::expr('sum(filesize)'), 'filesize'))
			->select(array(DB::expr('count(*)'), 'total'))
			->execute();

		// Get the asset count and total size from the result
		$size = $result->get('filesize');
		$total = $result->get('total');

		// Were any assets found?
		if ($total === 0)
		{
			// Nope, show a message explaining that we couldn't find anything.
			$this->template = View::factory("$this->_view_directory/none_found");
		}
		else
		{
			// Retrieve the results and load Model_Asset classes
			$assets = $query
				->select('assets.*')
				->limit($perpage)
				->offset(($page - 1) * $perpage)
				->as_object('Model_Asset')
				->execute();

			// Put everthing in the views.
			$this->template = View::factory("$this->_view_directory/list", array(
				'assets'		=>	$assets,
				'tag'			=>	$tag,
				'total_size'	=>	$size,
				'total'		=>	$total,
				'sortby'		=>	$sortby,
				'order'		=>	$order,
			));

			// How many pages are there?
			$pages = ceil($total / $perpage);

			if ($pages > 1)
			{
				// More than one page - generate pagination links.
				$url = '#tag/' . $this->request->query('tag');
				$pagination = View::factory('pagination/query', array(
					'current_page'		=>	$page,
					'total_pages'		=>	$pages,
					'base_url'			=>	$url,
					'previous_page'		=>	$page - 1,
					'next_page'		=>	($page == $pages) ? 0 : ($page + 1),
				));

				// Add the pagination view to the main view.
				$this->template->set('pagination', $pagination);
			}
		}
	}

	/**
	 * Display the asset manager.
	 * Used by the CMS assets page (/cms/assets) and for editing asset chunks, slideshows, feature images etc.
	 *
	 * This just makes internal requests for the filters and tags. This allows the possibility of making those requests asynchronous in future.
	 */
	public function action_manager()
	{
		$this->template = View::factory("$this->_view_directory/manager", array(
			'filters'	=>	Request::factory('cms/assets/filters')->execute(),
			'tags'	=>	Request::factory('cms/tags/tree')->post(array('type' => 1))->execute(),
		));
	}

	/**
	 * Save an asset.
	 *
	 */
	public function action_save()
	{
		// Load the asset data.
		$asset = new Model_Asset($this->request->param('id'));

		// Does the asset exist?
		if ( ! $asset->loaded())
		{
			throw new HTTP_Exception_404;
		}

		// Get the POST data.
		$post = $this->request->post();

		// Set the asset details.
		$asset
			->values(array(
				'title'			=>	$post['title'],
				'description'	=>	$post['description'],
				'visible_from'	=>	strtotime($post['visible_from']),
			))
			->update();

		// Redirect to the asset's page.
		$this->redirect('/cms/assets/view/' . $asset->id);
	}

	/**
	 * Add tags to a single or multiple assets.
	 *
	 * **Accepted POST parameters:**
	 * Name		|	Type		|	Description
	 * ---------------|-----------------|---------------
	 * tags		|	array	|	Array of tag IDs to be removed from the asset.
	 *
	 */
	public function action_tag()
	{
		// Get the IDs of the assets the tags are being applied to.
		$asset_ids = array_unique(explode("-", $this->request->param('id')));

		// Get the IDs of the tags which are being added.
		$tag_ids = (array) $this->request->post('tags');

		// Prepare the insert query object.
		$query = DB::insert('tags_applied');

		foreach ($asset_ids as $asset_id)
		{
			foreach ($tag_ids as $tag_id)
			{
				// Add the tag and asset ID to the query.
				$query->values(array(
					'tag_id'		=>	$tag_id,
					'asset_id'		=>	$asset_id,
					'object_type'	=>	Model_Tag_Applied::OBJECT_TYPE_ASSET,
				));
			}
		}

		// Execute the query.
		// Ignore database exceptions incase the person is applying a tag to an asset where it's already applied.
		try
		{
			$query->execute();
		}
		catch (Database_Exception $e) {}
	}

	/**
	 * Remove tags from an asset.
	 *
	 * **Accepted POST parameters:**
	 * Name		|	Type		|	Description
	 * ---------------|-----------------|---------------
	 * tags		|	array 	|	Array of tag IDs to be removed from the asset.
	 *
	 */
	public function action_untag()
	{
		// Load the asset data.
		$asset = new Model_Asset($this->request->param('id'));

		// Can't remove tags from an asset which doesn't exist.
		if ($asset->loaded())
		{
			// Get an array of tag IDs which are being removed.
			$tag_ids = (array) $this->request->post('tags');

			DB::delete('tags_applied')
				->where('object_type', '=', $asset->get_object_type_id())
				->where('object_id', '=', $asset->id)
				->where('tag_id', 'IN', $tag_ids)
				->execute();
		}
	}

	/**
	 * Controller to show an asset's view.
	 *
	 */
	public function action_view()
	{
		// Load the asset.
		$asset = new Model_Asset($this->request->param('id'));

		// Check that the asset exists
		if ( ! $asset->loaded())
		{
			throw new HTTP_Exception_404;
		}

		// If the asset is a BOTR video which isn't marked as encoded then attempt to update the information.
		if ($asset->type == Boom_Asset::BOTR AND ! $asset->encoded)
		{
			Request::factory('cms/video/sync/' . $asset->id)->execute();
			$asset->reload();
		}

		$this->template = View::factory("$this->_view_directory/view", array(
			'asset'	=>	$asset,
		));
	}
}