<?php

namespace BoomCMS;

class BoomCMS
{
    /**
     * The BoomCMS version.
     *
     * @var string
     */
    const VERSION = '4.4.0-Dev';

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
