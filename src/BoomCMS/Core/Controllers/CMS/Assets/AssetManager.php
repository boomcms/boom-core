<?php

namespace BoomCMS\Core\Controllers\CMS\Assets;

use BoomCMS\Core\Auth;
use BoomCMS\Core\Asset;
use BoomCMS\Core\Asset\Mimetype\Mimetype;
use BoomCMS\Core\Asset\Finder;
use BoomCMS\Core\Controllers\Controller;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
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
     * @var Asset\Provider
     */
    protected $provider;

    public function __construct(Auth\Auth $auth, Request $request, Asset\Provider $provider)
    {
        $this->auth = $auth;
        $this->request = $request;
        $this->provider = $provider;

        $this->authorization('manage_assets');
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

    public function get(Finder\Finder $finder)
    {
        $finder
            ->addFilter(new Finder\Tag($this->request->input('tag')))
            ->addFilter(new Finder\TitleContains($this->request->input('title')))
            ->addFilter(new Finder\Type($this->request->input('type')));

        $column = 'last_modified';
        $order = 'desc';

        if (strpos($this->request->input('sortby'), '-' ) > 1) {
            list($column, $order) = explode('-', $this->request->input('sortby'));
        }

        $finder->setOrderBy($column, $order);

        $count = $finder->count();

        if ($count === 0) {
            return View::make($this->viewPrefix . 'none_found');
        } else {
            $page = max(1, $this->request->input('page'));
            $perpage = max($this->perpage, $this->request->input('perpage'));
            $pages = ceil($count / $perpage);

            $assets = $finder
                ->setLimit($perpage)
                ->setOffset(($page - 1) * $perpage)
                ->findAll();

            return View::make($this->viewPrefix . 'list', [
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

    public function upload()
    {
        $asset_ids = $errors = [];
        $now = new DateTime('now');

        foreach ($this->request->file() as $files) {
            foreach ($files as $i => $file) {
                if ( ! $file->isValid()) {
                    $errors[] = $file->getErrorMessage();
                    continue;
                }

                try {
                    $mime = Mimetype::factory($file->getMimeType());
                } catch (Exception\UnsupportedMimeType $e) {
                    $errors[] = "File {$file->getClientOriginalName()} is of an unsuported type: {$e->getMimetype()}";
                    continue;
                }

                $asset = Asset\Asset::factory(['type' => $mime->getType()]);
                $asset
                    ->setUploadedBy($this->auth->getPerson())
                    ->setVisibleFrom($now)
                    ->setLastModified($now)
                    ->setTitle($file->getClientOriginalName())
                    ->setFilename($file->getClientOriginalName())
                    ->setFilesize($file->getClientSize());

                $asset_ids[] = $provider->save($asset)->getId();
                $file->move(Asset\Asset::directory(), $asset->getId());

                if ($asset->isImage()) {
                    list($width, $height) = getimagesize($filename);

                    $asset
                        ->setWidth($width)
                        ->setHeight($height);

                    $provider->save($asset);
                }
            }

            if (count($errors)) {
                return new JsonResponse($errors, 500);
            } else {
                return new JsonResponse($asset_ids);
            }
        }
    }

    public function view()
    {
        if ( ! $this->asset->loaded()) {
            throw new HTTP_Exception_404();
        }

        return View::make("$this->viewPrefix/view", [
            'asset' => $this->asset,
        ]);
    }
}
