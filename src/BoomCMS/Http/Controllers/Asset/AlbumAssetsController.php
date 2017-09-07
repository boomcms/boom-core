<?php

namespace BoomCMS\Http\Controllers\Asset;

use BoomCMS\Database\Models\Album;
use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Helpers;
use Illuminate\Http\Request;

class AlbumAssetsController extends Controller
{
    /**
     * The request input key which holds the assets to add / remove to / from the album.
     *
     * @var string
     */
    protected $assetsKey = 'assets';

    /**
     * @var string
     */
    protected $role = 'manageAlbums';

    public function destroy(Request $request, Album $album)
    {
        if ($request->has($this->assetsKey)) {
            $album->removeAssets($request->input($this->assetsKey));
        }

        return $album;
    }

    public function index(Album $album)
    {
        $params = ['album' => $album->getId()];

        return [
            'total'  => Helpers::countAssets($params),
            'assets' => Helpers::getAssets($params),
        ];
    }

    public function store(Request $request, Album $album)
    {
        if ($request->has($this->assetsKey)) {
            $album->addAssets($request->input($this->assetsKey));
        }

        return $album;
    }
}
