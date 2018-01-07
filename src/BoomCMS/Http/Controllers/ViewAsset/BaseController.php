<?php

namespace BoomCMS\Http\Controllers\ViewAsset;

use BoomCMS\Contracts\Models\Asset;
use BoomCMS\Http\Concerns\StreamsAssets;
use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Facades\Asset as AssetFacade;
use BoomCMS\Support\Facades\AssetVersion;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response as ResponseFacade;
use Intervention\Image\Constraint;
use Intervention\Image\ImageCache;
use Intervention\Image\ImageManager;

class BaseController extends Controller
{
    use StreamsAssets;

    /**
     * @var Asset
     */
    protected $asset;

    public function __construct(Request $request, Asset $asset)
    {
        $this->asset = $asset;
        $this->request = $request;
        $this->response = new Response();

        if ($request->has('version') && Auth::check()) {
            $asset->setVersion(AssetVersion::find($request->input('version')));
        }
    }

    public function thumb($width = null, $height = null)
    {
        if (!$this->asset->hasThumbnail()) {
            $path = __DIR__."/../../../../../public/img/extensions/{$this->asset->getExtension()}.png";

            return ResponseFacade::file($path, $this->getHeaders());
        }

        $thumbnail = $this->asset->getThumbnail();

        if (empty($width) && empty($height)) {
            return $this->streamAsset($thumbnail);
        }

        $image = (new ImageManager())->cache(function (ImageCache $cache) use ($width, $height, $thumbnail) {
            $width = empty($width) ? null : $width;
            $height = empty($height) ? null : $height;

            return $cache
                ->make(AssetFacade::path($thumbnail))
                ->resize($width, $height, function (Constraint $constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('image/png');
        });

        return $this->addHeaders($this->response)->setContent($image);
    }

    public function view($width = null, $height = null)
    {
        return $this->streamAsset($this->asset)
            ->partial($this->request->header('Range'))
            ->toResponse();
    }
}
