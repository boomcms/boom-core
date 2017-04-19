<?php

namespace BoomCMS\Http\Controllers\ViewAsset;

use BoomCMS\Contracts\Models\Asset;
use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Facades\Asset as AssetFacade;
use BoomCMS\Support\Facades\AssetVersion;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Constraint;
use Intervention\Image\ImageCache;
use Intervention\Image\ImageManager;

class BaseController extends Controller
{
    /**
     * @var Asset
     */
    protected $asset;

    public function __construct(Request $request, Asset $asset)
    {
        $this->asset = $asset;
        $this->response = new Response();

        if ($request->has('version') && Auth::check()) {
            $asset->setVersion(AssetVersion::find($request->input('version')));
        }
    }

    public function view($width = null, $height = null)
    {
        return $this->response->file(AssetFacade::file($this->asset), [
            'content-disposition' => 'inline; filename="'.$this->asset->getOriginalFilename().'"',
        ]);
    }

    public function thumb($width = null, $height = null)
    {
        if (!$this->asset->hasThumbnail()) {
            return $this->response
                ->header('Content-type', 'image/png')
                ->setContent(readfile(__DIR__."/../../../../../public/img/extensions/{$this->asset->getExtension()}.png"));
        }

        $thumbnail = $this->asset->getThumbnail();
        $im = new ImageManager();

        if ($width || $height) {
            $image = $im->cache(function (ImageCache $cache) use ($width, $height, $thumbnail) {
                $width = empty($width) ? null : $width;
                $height = empty($height) ? null : $height;

                return $cache
                    ->make(AssetFacade::file($thumbnail))
                    ->resize($width, $height, function (Constraint $constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })
                    ->encode('image/png');
            });
        } else {
            $image = $im->make(AssetFacade::file($thumbnail))->encode();
        }

        return $this->response
                ->header('content-type', $thumbnail->getMimetype())
                ->setContent($image);
    }
}
