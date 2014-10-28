<?php

use \Boom\Asset as Asset;
use \Boom\Asset\Finder as AssetFinder;

class Controller_Cms_Assets extends Controller_Cms
{
	protected $perpage = 30;

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
		$finder = new AssetFinder;
		$finder
			->addFilter(new  \Boom\Asset\Finder\Filter\Tag($this->request->post('tag')))
			->addFilter(new \Boom\Asset\Finder\Filter\TitleContains($this->request->post('title')));

		$column = 'last_modified';
		$order = 'desc';

		if (strpos($this->request->post('sortby'), '-' ) > 1) {
			list($column, $order) = explode('-', $this->request->post('sortby'));
		}

		$finder->setOrderBy($column, $order);

		if ($type = $this->request->post('type')) {
//			$finder->addFilter(new \Boom\Asset\Finder\Filter\Type($type));
		}

		$count = $finder->count();

		if ($count === 0) {
			$this->template = new View("$this->viewDirectory/none_found");
		} else {
			$page = max(1, $this->request->post('page'));
			$perpage = max($this->perpage, $this->request->post('perpage'));
			$pages = ceil($count / $perpage);

			$assets = $finder
				->setLimit($perpage)
				->setOffset(($page - 1) * $perpage)
				->findAll();

			$this->template = new View("$this->viewDirectory/list", array(
				'assets' => $assets,
				'total' => $count,
				'order' =>	 $order,
				'pages' => $pages
			));
		}
	}

	/**
	 * Display the asset manager without topbar etc.
	 *
	 */
	public function action_manager()
	{
		$this->template = new View("$this->viewDirectory/manager");
	}

	public function action_picker()
	{
		$finder = new AssetFinder;
		$totalAssets = $finder->count();

		$assets = $finder
			->setLimit($this->perpage)
			->setOrderBy('last_modified', 'desc')
			->findAll();

		$this->template = new View("$this->viewDirectory/picker", array(
			'assets' => $assets,
			'pages' => ceil($totalAssets / $this->perpage)
		));
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

		$finder = new \Boom\Tag\Finder;
		$tags = $finder
			->addFilter(new \Boom\Tag\Finder\Filter\Asset($this->asset))
			->setOrderBy('name', 'asc')
			->findAll();

		$this->template = View::factory("$this->viewDirectory/view", array(
			'asset' => $this->asset,
			'tags' => $tags
		));
	}
}