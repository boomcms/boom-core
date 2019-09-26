<?php

namespace BoomCMS\Http\Controllers\Asset;

use BoomCMS\Database\Models\Album;
use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Facades\Album as AlbumFacade;
use Illuminate\Http\Request;
use BoomCMS\Support\Helpers;

class AlbumController extends Controller
{
    /**
     * @var string
     */
    protected $viewPrefix = 'boomcms::assets.';

    public function destroy(Album $album)
    {
        AlbumFacade::delete($album);
    }

    public function index(Request $request)
    {
        if ($request->filled('assets')) {
            return AlbumFacade::findByAssetIds($request->input('assets'));
        }

        return AlbumFacade::all();
    }

    public function show(Album $album)
    {
        return $album;
    }

    public function store(Request $request)
    {
        $name = $request->input(Album::ATTR_NAME, 'Untitled');
        $description = $request->input(Album::ATTR_DESCRIPTION);

        return AlbumFacade::create($name, $description);
    }

    public function update(Album $album, Request $request)
    {
        $expected = [Album::ATTR_NAME, Album::ATTR_DESCRIPTION, Album::ATTR_ORDER];
        $album->fill($request->all($expected));

        return AlbumFacade::save($album);
    }

    /**
     * Display the asset list
     *
     * @return View
     */
    public function list(Request $request)
    {
        $album = AlbumFacade::findBySlug($request->segment(4));
        $params = ['album' => $album->getId()];

        return view($this->viewPrefix.'album', ['album' => $album, 'assets' => Helpers::getAssets($params)]);
    }
}
