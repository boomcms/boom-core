<?php

namespace Boom\Asset\Processor;

class Video extends Processor
{
    public function thumbnail($width = null, $height = null)
    {
        return $this->response
            ->headers('Content-type', 'image/gif')
            ->body(readfile(__DIR__.'/../../../../media/boom/img/icons/40x40/mov_icon.gif'));
    }

    public function view($width = null, $height = null)
    {
        return $this->response
            ->send_file($this->asset->getFilename(), $this->asset->getFilename(), [
                'inline' => true,
                'mime_type' => $this->asset->getMimetype(),
                'resumable' => true,
            ]);
    }
}
