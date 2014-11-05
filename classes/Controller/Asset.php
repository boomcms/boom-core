<?php

use Boom\Asset;

class Controller_Asset extends Boom\Controller
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

        if ( ! $this->asset->loaded() || (Kohana::$environment !== Kohana::DEVELOPMENT && ! $this->asset->exists()))
        {
            throw new HTTP_Exception_404;
        }

        if ( ! $this->asset->isVisible() && ! $this->auth->isLoggedIn()) {
            throw new HTTP_Exception_404();
        }

        if ($this->enable_caching) {
            $this->response->headers('Cache-Control', 'public, max-age='.$this->max_age);
            HTTP::check_cache($this->request, $this->response, $this->asset->getLastModified()->getTimestamp());
        }

        $processor = 'Asset\\Processor\\' . class_basename($this->asset);
        $this->processor = new $processor($this->asset, $this->response);
    }

    public function action_embed()
    {
        $this->response = $this->processor->embed();
//        $this->response->body(HTML::anchor('asset/view/'.$this->asset->getId(), "Download {$this->asset->getTitle()}"));
    }

    public function action_view()
    {
        $this->response = $this->processor->view();
    }

    public function action_thumb()
    {

    }

    public function action_download()
    {
        $this->_log_download();
        $this->_do_download('download');
    }

    protected function _do_download($method = 'inline')
    {
        $this->response
            ->headers([
                'Content-Type'                =>    (string) $this->asset->getMimetype(),
                'Content-Disposition'            =>    "$method; filename='{$this->asset->filename}'",
                'Content-Transfer-Encoding'    =>    'binary',
                'Content-Length'            =>    $this->asset->filesize,
                'Accept-Ranges'                =>    'bytes',
            ])
            ->body(readfile($this->asset->getFilename()));
    }

    protected function _log_download()
    {
        if ( ! $this->auth->isLoggedIn()) {
            $this->asset->log_download(Request::$client_ip);
        }
    }
}
