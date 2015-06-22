<?php

namespace BoomCMS\Core\Controllers\Asset;

use BoomCMS\Core\Auth;
use BoomCMS\Core\Asset;
use BoomCMS\Core\Controllers\Controller;

use Illuminate\Http\Response;

abstract class BaseController extends Controller
{
    /**
     *
     * @var Asset\Asset
     */
    protected $asset;

    public function __construct(Auth\Auth $auth, Asset\Asset $asset)
    {
        $this->auth = $auth;
        $this->asset = $asset;
		$this->response = new Response();
    }

    public function download()
    {
        return response()->download($this->asset->getFilename());
    }

    public function embed()
    {
        return "<a class='download' href='/asset/view/{$this->asset->getId()}'>{$this->asset->getTitle()}</a>";
    }

    public function view($width = null, $height = null)
    {
       return $this->response
            ->header('content-type', $this->asset->getMimetype())
            ->header('content-disposition', 'inline; filename="' . $this->asset->getOriginalFilename() . '"')
            ->header('content-transfer-encoding', 'binary')
            ->header('Content-Length', $this->asset->getFilesize())
            ->header('Accept-Ranges', 'bytes')
            ->setContent(file_get_contents($this->asset->getFilename()));
    }

    abstract public function thumb($width = null, $height = null);
}
