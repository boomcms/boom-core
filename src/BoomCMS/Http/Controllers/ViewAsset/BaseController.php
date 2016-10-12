<?php

namespace BoomCMS\Http\Controllers\ViewAsset;

use BoomCMS\Contracts\Models\Asset;
use BoomCMS\Http\Controllers\Controller;
use Intervention\Image\Constraint;
use Intervention\Image\ImageCache;
use Intervention\Image\ImageManager;
use Illuminate\Http\Response;

class BaseController extends Controller
{
    /**
     * @var Asset
     */
    protected $asset;

    public function __construct(Asset $asset)
    {
        $this->asset = $asset;
        $this->response = new Response();

        if (!$this->asset->exists()) {
            abort(404);
        }
    }

    public function download()
    {
        return response()->download(
            $this->asset->getFilename(),
            $this->asset->getOriginalFilename()
        );
    }

    public function embed()
    {
        return $this->asset->getEmbedHtml();
    }

    public function view($width = null, $height = null)
    {
        return $this->response
            ->header('content-type', $this->asset->getMimetype())
            ->header('content-disposition', 'inline; filename="'.$this->asset->getOriginalFilename().'"')
            ->header('content-transfer-encoding', 'binary')
            ->header('content-length', $this->asset->getFilesize())
            ->header('accept-ranges', 'bytes')
            ->setContent(file_get_contents($this->asset->getFilename()));
    }

    public function thumb($width = null, $height = null)
    {
        if (!$this->asset->hasThumbnail()) {
            return $this->response
                ->header('Content-type', 'image/png')
                ->setContent(readfile(__DIR__."/../../../../../public/img/extensions/{$this->asset->getExtension()}.png"));
        }

        $thumbnail = $this->asset->getThumbnail();
        $filename = $thumbnail->getFilename();
        $im = new ImageManager();

        if ($width || $height) {
            $image = $im->cache(function (ImageCache $cache) use ($width, $height, $filename) {
                $width = empty($width) ? null : $width;
                $height = empty($height) ? null : $height;

                return $cache
                    ->make($filename)
                    ->resize($width, $height, function (Constraint $constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })
                    ->encode('image/png');
                });
        } else {
            $image = $im->make($filename)->encode();
        }

        return $this->response
                ->header('content-type', $thumbnail->getMimetype())
                ->setContent($image);
    }
}
