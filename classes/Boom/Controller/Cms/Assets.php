<?php defined('SYSPATH') OR die('No direct script access.');

/**
  * @package	BoomCMS
  * @category	Assets
  * @category	Controllers
  */
class Boom_Controller_Cms_Assets extends Controller_Cms
{
	/**
	 *
	 * @var	string	Directory where the view files used in this class are stored.
	 */
	protected $_view_directory = 'boom/assets';

	/**
	 *
	 * @var Model_Asset
	 */
	public $asset;

	/**
	 * Check that they can manage assets.
	 */
	public function before()
	{
		parent::before();

		// Permissions check.
		$this->authorization('manage_assets');

		// Instantiate an asset model.
		$this->asset = new Model_Asset($this->request->param('id'));
	}

	/**
	 * Delete multiple assets at a time.
	 *
	 * Takes an array if asset IDs and calls [Model_Asset::delete()] on each one.
	 *
	 * @uses	Model_Asset::delete()
	 * @uses	Boom_Controller::log()
	 */
	public function action_delete()
	{
		$this->_csrf_check();

		if ($this->asset->loaded())
		{
			$this->asset->delete();
		}

		$asset_ids = array_unique((array) $this->request->post('assets'));

		foreach ($asset_ids as $asset_id)
		{
			$this->asset
				->where('id', '=', $asset_id)
				->find();

			if ( ! $this->asset->loaded())
			{
				// Move along, nothing to see here.
				continue;
			}

			$this->log("Deleted asset $this->asset->title (ID: $this->asset->id)");

			$this->asset
				->delete()
				->clear();
		}
	}

	/**
	 * Display the asset manager.
	 *
	 */
	public function action_index()
	{
		$this->template = View::factory("$this->_view_directory/index", array(
			'manager'	=>	Request::factory('cms/assets/manager')->execute()->body(),
			'person'	=>	$this->person,
		));
	}

	public function action_list()
	{
		$finder = new Asset_Finder;
		$finder
			->by_tags(explode("-", $this->request->query('tag')))
			->by_title($this->request->query('title'));

		$column = 'last_modified';
		$order = 'desc';

		if (strpos($this->request->query('sortby'), '-' ) > 1)
		{
			list($column, $order) = explode('-', $this->request->query('sortby'));
			$finder->order_by($column, $order);
		}
		else
		{
			$finder->order_by('last_modified', 'desc');
		}

		$type = $this->request->query('type') AND $finder->by_type($type);

		$count_and_size = $finder->get_count_and_total_size();
		$total = $count_and_size['total'];
		$filesize = $count_and_size['filesize'];

		if ($total === 0)
		{
			$this->template = View::factory("$this->_view_directory/none_found");
		}
		else
		{
			$page = max(1, $this->request->query('page'));
			$perpage = max(30, $this->request->query('perpage'));
			$assets = $finder->get_assets($perpage, ($page - 1) * $perpage);

			$this->template =new View("$this->_view_directory/list", array(
				'assets'		=>	$assets,
				'total_size'	=>	$filesize,
				'total'		=>	$total,
				'order'		=>	$order,
			));

			$pages = ceil($total / $perpage);

			if ($pages > 1)
			{
				// More than one page - generate pagination links.
				$pagination = new View('pagination/query', array(
					'current_page'		=>	$page,
					'total_pages'		=>	$pages,
					'base_url'			=>	'',
					'previous_page'		=>	$page - 1,
					'next_page'		=>	($page == $pages) ? 0 : ($page + 1),
				));

				$this->template->set('pagination', $pagination);
			}
		}
	}

	/**
	 * Display the asset manager without topbar etc.
	 *
	 */
	public function action_manager()
	{
		$this->template = View::factory("$this->_view_directory/manager");
	}

	public function action_restore()
	{
		$timestamp = $this->request->query('timestamp');

		if (file_exists($this->asset->get_filename().".".$timestamp.".bak"))
		{
			// Backup the current active file.
			@rename($this->asset->get_filename(), $this->asset->get_filename().".".$_SERVER['REQUEST_TIME'].".bak");

			// Restore the old file.
			@copy($this->asset->get_filename().".".$timestamp.".bak", $this->asset->get_filename());
		}

		$this->asset
			->delete_cache_files()
			->set('last_modified', $_SERVER['REQUEST_TIME'])
			->update();

		// Go back to viewing the asset.
		$this->redirect('/cms/assets/#asset/'.$this->asset->id);
	}

	public function action_save()
	{
		$this->_csrf_check();

		if ( ! $this->asset->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$this->asset
			->values($this->request->post(), array('title','description','visible_from', 'thumbnail_asset_id', 'credits'))
			->update();
	}

	public function action_view()
	{
		if ( ! $this->asset->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$this->template = View::factory("$this->_view_directory/view", array(
			'asset'	=>	$this->asset,
			'tags'	=>	$this->asset
				->tags
				->order_by('name', 'asc')
				->find_all()
		));
	}
}