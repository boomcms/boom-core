<?php

namespace BoomCMS\Core\Controllers\CMS\Assets;

use BoomCMS\Core\Controllers\Controller;

use Illuminate\Support\Facades\View;

class AssetManager extends Controller
{
    protected $perpage = 30;

    /**
	 *
	 * @var	string
	 */
    protected $viewPrefix = 'boom::assets.';

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

    public function delete()
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
    public function index()
    {
        return View::make($this->viewPrefix . 'index', [
            'manager' => $this->manager(),
            'person' => $this->person,
        ]);
    }

    public function listAssets()
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
            return View::make("$this->viewDirectory/none_found");
        } else {
            $page = max(1, $this->request->input('page'));
            $perpage = max($this->perpage, $this->request->input('perpage'));
            $pages = ceil($count / $perpage);

            $assets = $finder
                ->setLimit($perpage)
                ->setOffset(($page - 1) * $perpage)
                ->findAll();

            return View::make("$this->viewDirectory/list", [
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
    public function manager()
    {
        return View::make($this->viewPrefix . 'manager');
    }

    public function picker()
    {
        return View::make($this->viewPrefix . 'picker');
    }

    public function restore()
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

    public function save()
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

    public function view()
    {
        if ( ! $this->asset->loaded()) {
            throw new HTTP_Exception_404();
        }

        return View::make("$this->viewDirectory/view", [
            'asset' => $this->asset,
        ]);
    }
}
