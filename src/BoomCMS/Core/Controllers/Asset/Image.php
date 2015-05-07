<?php

namespace BoomCMS\Core\Controllers\Asset;

use BoomCMS\Core\Asset\Asset;

use Intervention\Image\ImageManager;

class Image extends BaseController
{
    /**
     *
     * @var ImageManager
     */
    private $manager;

    public function __construct(Asset $asset)
    {
        parent::__construct($asset, $response);

        $this->manager = new ImageManager([
            'cache' => [
                'path' => Boom\Boom::instance()->getCacheDir() . 'assets',
            ]
        ]);
    }

    public function crop($width = null, $height = null)
    {
        if ($width && $height) {
            $image = $this->manager->cache(function ($manager) use ($width, $height) {
                return $manager->make($this->asset->getFilename())->fit($width, $height);
            });
        } else {
            $image = $this->manager->make($this->asset->getFilename())->encode();
        }

        return $this->response
                ->header('content-type', $this->asset->getMimetype())
                ->body($image);
    }

    public function thumbnail($width = null, $height = null)
    {
        return $this->view($width, $height);
    }

    public function view($width = null, $height = null)
    {
        $filename = $this->asset->exists() ? $this->asset->getFilename() : __DIR__ . '/../../../../public/boom/img/placeholder.png';

        if ($width || $height) {
            $image = $this->manager->cache(function ($manager) use ($width, $height, $filename) {
                return $manager->make($filename)->resize($width != 0 ? $width : null, $height != 0 ? $height : null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            });
        } else {
            $image = $this->manager->make($this->asset->getFilename())->encode();
        }

        return $this->response
            ->header('content-type', (string) $this->asset->getMimetype())
            ->body($image);
    }

    public function embed()
    {
       return $this->response
            ->body("<img src='/asset/view/{$this->asset->getId()}' />");

    }
}
