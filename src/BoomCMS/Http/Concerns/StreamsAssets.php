<?php

namespace BoomCMS\Http\Concerns;

use BoomCMS\Contracts\Models\Asset as AssetInterface;
use BoomCMS\Support\Facades\Asset as AssetFacade;
use RobbyTaylor\FileStream\StreamsFiles;

trait StreamsAssets
{
    use StreamsFiles;

    public function streamAsset(AssetInterface $asset)
    {
        return $this->stream(AssetFacade::filesystem($asset)->getDriver(), $asset->getPath($asset))
            ->mimetype($asset->getMimetype())
            ->size($asset->getFilesize())
            ->filename($asset->getOriginalFilename());
    }
}
