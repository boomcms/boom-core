<?php

namespace BoomCMS;

class BoomCMS
{
    /**
     * The BoomCMS version.
     *
     * @var string
     */
    const VERSION = '5.0.13';

    /**
     * Returns the BoomCMS version.
     *
     * @return string
     */
    public function getVersion()
    {
        return self::VERSION;
    }
}
