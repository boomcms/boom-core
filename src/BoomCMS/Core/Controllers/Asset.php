<?php

namespace BoomCMS\Core\Controllers;

use Boom\Asset;

class Asset extends Controller
{
    /**
     *
     * @var Asset\Asset
     */
    private $asset;

    public $enable_caching = true;

    /**
	 * The value to use for the max-age header.
	 *
	 * @var integer
	 */
    public $max_age = Date::MONTH;

    /**
     *
     * @var Asset\Processor
     */
    private $processor;

    public function before()
    {
        parent::before();

        $this->asset = Asset\Factory::byId($this->request->param('id'));

        if ( ! $this->asset->loaded() || ($this->environment->isDevelopment() && ! $this->asset->exists())) {
            throw new HTTP_Exception_404();
        }

        if ( ! $this->asset->isVisible() && ! $this->auth->isLoggedIn()) {
            throw new HTTP_Exception_404();
        }

        if ($this->enable_caching) {
            $this->response->headers('Cache-Control', 'public, max-age='.$this->max_age);
            HTTP::check_cache($this->request, $this->response, $this->asset->getLastModified()->getTimestamp());
        }

        $processor = 'Boom\\Asset\\Processor\\' . class_basename($this->asset);
        $this->processor = new $processor($this->asset, $this->response);
    }

    public function crop()
    {
        $this->response = $this->processor->crop($this->request->param('width'), $this->request->param('height'));
    }

    public function embed()
    {
        $this->response = $this->processor->embed();
    }

    public function view()
    {
        $this->response = $this->processor->view($this->request->param('width'), $this->request->param('height'));
    }

    public function thumb()
    {
        $this->response = $this->processor->thumbnail($this->request->param('width'), $this->request->param('height'));
    }

    public function download()
    {
        if ( ! $this->auth->isLoggedIn()) {
            $this->asset->logDownload(Request::$client_ip);
        }

        $this->response = $this->processor->download();
    }
}
