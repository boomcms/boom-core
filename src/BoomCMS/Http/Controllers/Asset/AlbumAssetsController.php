<?php

namespace BoomCMS\Http\Controllers\Asset;

use BoomCMS\Database\Models\Album;
use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Helpers;
use Illuminate\Http\Request;

class AlbumAssetsController extends Controller
{
    /**
     * @var string
     */
    protected $role = 'manageAlbums';

    public function destroy(Request $request, Album $album)
    {
        $album->removeAssets($request->input('assets'));

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
        $album->addAssets($request->input('assets'));

        return $album;
    }
}
