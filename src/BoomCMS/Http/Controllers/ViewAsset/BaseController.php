<?php

namespace BoomCMS\Http\Controllers\ViewAsset;

use BoomCMS\Contracts\Models\Asset;
use BoomCMS\Http\Controllers\Controller;
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
        return $this->response
            ->header('Content-type', 'image/png')
            ->setContent(readfile(__DIR__."/../../../../../public/img/extensions/{$this->asset->getExtension()}.png"));
    }
}
