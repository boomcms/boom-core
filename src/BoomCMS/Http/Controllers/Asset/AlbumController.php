<?php

namespace BoomCMS\Http\Controllers\Asset;

use BoomCMS\Database\Models\Album;
use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Facades\Album as AlbumFacade;
use Illuminate\Http\Request;

class AlbumController extends Controller
{
    /**
     * @var string
     */
    protected $role = 'manageAlbums';

    public function destroy(Album $album)
    {
        AlbumFacade::delete($album);
    }

    public function index(Request $request)
    {
        if ($request->has('assets')) {
            return AlbumFacade::findByAssetIds($request->input('assets'));
        }

        return AlbumFacade::all();
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
        $album->fill($request->only($expected));

        return AlbumFacade::save($album);
    }
}
