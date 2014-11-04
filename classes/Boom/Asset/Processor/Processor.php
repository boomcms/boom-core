<?php

namespace Boom\Asset\Processor;

use Boom\Asset\Asset;
use Response;

abstract class Processor
{
    /**
	 *
	 * @var \Boom\Asset\Asset
	 */
    protected $asset;

    /**
	 *
	 * @var \Response
	 */
    protected $response;

    /**
	 *
	 * @param \Boom\Asset\Asset $asset
	 * @param Response $response
	 */
    public function __construct(Asset $asset, Response $response)
    {
        $this->asset = $asset;
        $this->response = $response;
    }

    public function download()
    {

    }

    public function embed()
    {

    }

    public function view()
    {

    }

    public function thumbnail()
    {

    }
}
