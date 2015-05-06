<?php

namespace BoomCMS\Core\Controllers\CMS\Assets;

use BoomCMS\Core\Controllers\Controller;
use BoomCMS\Core\Asset\Provider;

class AssetManager extends Controller
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

    protected $assetProvider;

    public function before()
    {
        parent::before();

        $this->authorization('manage_assets');
        $this->asset = Asset\Factory::byId($this->request->param('id'));
    }

    public function action_delete()
    {
        $assetIds = array_unique((array) $this->request->input('assets'));

        foreach ($assetIds as $assetId) {
            $asset = Asset\Factory::byId($assetId);

            $commander = new \Boom\Asset\Commander($asset);
            $commander
                ->addCommand(new \Boom\Asset\Delete\CacheFiles())
                ->addCommand(new \Boom\Asset\Delete\OldVersions())
                ->addCommand(new \Boom\Asset\Delete\FromDatabase())
                ->addCommand(new \Boom\Asset\Delete\File())
                ->execute();

            $this->log("Deleted asset {$asset->getTitle()} (ID: {$asset->getId()})");
        }
    }

    /**
	 * Display the asset manager.
	 *
	 */
    public function action_index()
    {
        $this->template = View::factory("$this->viewDirectory/index", [
            'manager'    =>    Request::factory('cms/assets/manager')->execute()->body(),
            'person'    =>    $this->person,
        ]);
    }

    public function action_list()
    {
        $finder = new AssetFinder();
        $finder
            ->addFilter(new \Boom\Asset\Finder\Filter\Tag($this->request->input('tag')))
            ->addFilter(new \Boom\Asset\Finder\Filter\TitleContains($this->request->input('title')))
            ->addFilter(new \Boom\Asset\Finder\Filter\Type($this->request->input('type')));

        $column = 'last_modified';
        $order = 'desc';

        if (strpos($this->request->input('sortby'), '-' ) > 1) {
            list($column, $order) = explode('-', $this->request->input('sortby'));
        }

        $finder->setOrderBy($column, $order);

        $count = $finder->count();

        if ($count === 0) {
            $this->template = new View("$this->viewDirectory/none_found");
        } else {
            $page = max(1, $this->request->input('page'));
            $perpage = max($this->perpage, $this->request->input('perpage'));
            $pages = ceil($count / $perpage);

            $assets = $finder
                ->setLimit($perpage)
                ->setOffset(($page - 1) * $perpage)
                ->findAll();

            $this->template = new View("$this->viewDirectory/list", [
                'assets' => $assets,
                'total' => $count,
                'order' =>     $order,
                'pages' => $pages,
                'page' => $page
            ]);
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
        $this->template = new View("$this->viewDirectory/picker");
    }

    public function action_restore()
    {
        $timestamp = $this->request->query('timestamp');

        if (file_exists($this->asset->getFilename().".".$timestamp.".bak")) {
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
        if ( ! $this->asset->loaded()) {
            throw new HTTP_Exception_404();
        }

        $this->asset
            ->setTitle($this->request->input('title'))
            ->setDescription($this->request->input('description'))
            ->setVisiblefrom(new DateTime($this->request->input('visible_from')))
            ->setCredits($this->request->input('credits'))
            ->setThumbnailAssetId($this->request->input('thumbnail_asset_id'))
            ->save();
    }

    public function action_view()
    {
        if ( ! $this->asset->loaded()) {
            throw new HTTP_Exception_404();
        }

        $this->template = View::factory("$this->viewDirectory/view", [
            'asset' => $this->asset,
        ]);
    }
}
