<?php defined('SYSPATH') OR die('No direct script access.');

use \Boom\Asset as Asset;
use \Boom\Finder\Asset as AssetFinder;

class Boom_Controller_Cms_Assets extends Controller_Cms
{
	/**
	 *
	 * @var	string
	 */
	protected $viewDirectory = 'boom/assets';

	/**
	 *
	 * @var Asset
	 */
	public $asset;

	public function before()
	{
		parent::before();

		$this->authorization('manage_assets');
		$this->asset = AssetFinder::byId($this->request->param('id'));
	}

	public function action_delete()
	{
		$this->_csrf_check();

		if ($this->asset->loaded()) {
			$this->_deleteAsset($this->asset);
		}

		$asset_ids = array_unique((array) $this->request->post('assets'));

		foreach ($asset_ids as $asset_id) {
			$asset = \Boom\Asset\Finder::byId($asset_id);
			$this->_deleteAsset($asset);

			$this->log("Deleted asset {$this->asset->getTitle()} (ID: $this->asset->getId())");
		}
	}

	protected function _deleteAsset(\Boom\Asset $asset)
	{
		$commander = new \Boom\Asset\Commander($asset);
		$commander
			->addCommand(new \Boom\Asset\Delete\CacheFiles)
			->addCommand(new \Boom\Asset\Delete\OldVersions)
			->addCommand(new \Boom\Asset\Delete\FromDatabase)
			->addCommand(new \Boom\Asset\Delete\File)
			->execute();
	}

	/**
	 * Display the asset manager.
	 *
	 */
	public function action_index()
	{
		$this->template = View::factory("$this->viewDirectory/index", array(
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

		($type = $this->request->query('type')) && $finder->by_type($type);

		$count_and_size = $finder->get_count_and_total_size();
		$count = $count_and_size['count'];
		$filesize = $count_and_size['filesize'];

		if ($count === 0)
		{
			$this->template = View::factory("$this->viewDirectory/none_found");
		}
		else
		{
			$page = max(1, $this->request->query('page'));
			$perpage = max(30, $this->request->query('perpage'));
			$assets = $finder->get_assets($perpage, ($page - 1) * $perpage);

			$this->template =new View("$this->viewDirectory/list", array(
				'assets'		=>	$assets,
				'total_size'	=>	$filesize,
				'total'		=>	$count,
				'order'		=>	$order,
			));

			$pages = ceil($count / $perpage);

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
		$this->template = View::factory("$this->viewDirectory/manager");
	}

	public function action_restore()
	{
		$timestamp = $this->request->query('timestamp');

		if (file_exists($this->asset->getFilename().".".$timestamp.".bak"))
		{
			// Backup the current active file.
			@rename($this->asset->getFilename(), $this->asset->getFilename().".".$_SERVER['REQUEST_TIME'].".bak");

			// Restore the old file.
			@copy($this->asset->getFilename().".".$timestamp.".bak", $this->asset->getFilename());
		}

		$this->asset
			->delete_cache_files()
			->set('last_modified', $_SERVER['REQUEST_TIME'])
			->update();

		// Go back to viewing the asset.
		$this->redirect('/cms/assets/#asset/'.$this->asset->getId());
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

		$this->template = View::factory("$this->viewDirectory/view", array(
			'asset'	=>	$this->asset,
			'tags'	=>	$this->asset
				->tags
				->order_by('name', 'asc')
				->find_all()
		));
	}
}