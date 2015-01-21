<?php

namespace Boom\Asset\Processor;

use Response;
use Boom\Asset\Asset;

abstract class Processor
{
    /**
     *
     * @var Asset
     */
    protected $asset;

    /**
     *
     * @var Response
     */
    protected $response;

    /**
     *
     * @param Asset    $asset
     * @param Response $response
     */
    public function __construct(Asset $asset, Response $response)
    {
        $this->asset = $asset;
        $this->response = $response;
    }

    public function download()
    {
        return $this->response->headers([
            'content-type' => $this->asset->getMimetype(),
            'content-disposition' => 'attachment; filename="' . $this->asset->getOriginalFilename() . '"',
            'content-transfer-encoding' => 'binary',
            'Content-Length' => $this->asset->getFilesize(),
            'Accept-Ranges' => 'bytes',
        ])
        ->body(file_get_contents($this->asset->getFilename()));
    }

    public function embed()
    {
        return $this->response
            ->body("<a class='download' href='/asset/view/{$this->asset->getId()}'>{$this->asset->getTitle()}</a>");
    }

    public function view($width = null, $height = null)
    {
       return $this->response->headers([
            'content-type' => $this->asset->getMimetype(),
            'content-disposition' => 'inline; filename="' . $this->asset->getOriginalFilename() . '"',
            'content-transfer-encoding' => 'binary',
            'Content-Length' => $this->asset->getFilesize(),
            'Accept-Ranges' => 'bytes',
        ])
        ->body(file_get_contents($this->asset->getFilename()));
    }

    abstract public function thumbnail($width = null, $height = null);
}
