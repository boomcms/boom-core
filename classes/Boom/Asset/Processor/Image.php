<?php

namespace Boom\Asset\Processor;

use Boom\Asset\Asset;
use Response;
use Intervention\Image\ImageManager;

class Image extends Processor
{
    /**
     *
     * @var ImageManager
     */
    private $manager;

    public function __construct(Asset $asset, Response $response)
    {
        parent::__construct($asset, $response);

        $this->manager = new ImageManager([
            'cache' => [
                'path' => APPPATH . 'cache/assets',
            ]
        ]);
    }

    public function crop($width = null, $height = null)
    {
        if ($width || $height) {
            $image = $this->manager->cache(function($manager) use ($width, $height) {
                return $manager->make($this->asset->getFilename())->crop($width, $height);
            });
        } else {
            $image = $this->manager->make($this->asset->getFilename())->encode();
        }

        return $this->response
                ->headers('content-type', $this->asset->getMimetype())
                ->body($image);
    }

    public function thumbnail($width = null, $height = null)
    {
        return $this->view($width, $height);
    }

    public function view($width = null, $height = null)
    {
        if ($width || $height) {
            $image = $this->manager->cache(function($manager) use ($width, $height) {
                return $manager->make($this->asset->getFilename())->resize($width !== 0?: null, $height !== 0?: null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            });
        } else {
            $image = $this->manager->make($this->asset->getFilename())->encode();
        }

        return $this->response
            ->headers('content-type', $this->asset->getMimetype())
            ->body($image);
    }

    public function embed()
    {
       return $this->response
            ->body("<img src='/asset/view/{$this->asset->getId()}' />");

    }
}