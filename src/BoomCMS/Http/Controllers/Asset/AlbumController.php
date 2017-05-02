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

    public function update(Album $album, Request $request)
    {
        $expected = [Album::ATTR_NAME, Album::ATTR_DESCRIPTION, Album::ATTR_ORDER];
        $album->fill($request->only($expected));

        return AlbumFacade::save($album);
    }
}
