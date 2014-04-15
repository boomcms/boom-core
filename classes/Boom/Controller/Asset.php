<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Asset controller.
 *
 * @package		BoomCMS
 * @category	Assets
 * @category	Controllers
 * @author		Rob Taylor
 * @copyright	Hoop Associates
 */
abstract class Boom_Controller_Asset extends Boom_Controller
{
	/**
	 *
	 * @var Model_Asset
	 */
	public $asset;

	public $enable_caching = true;

	/**
	 * The value to use for the max-age header.
	 *
	 * @var integer
	 */
	public $max_age = Date::MONTH;

	public function before()
	{
		parent::before();

		$this->asset = $this->request->param('asset');

		if ( ! $this->asset->is_visible() AND ! $this->auth->logged_in())
		{
			throw new HTTP_Exception_404;
		}

		if ($this->enable_caching)
		{
			$this->response->headers('Cache-Control', 'public, max-age='.$this->max_age);
			HTTP::check_cache($this->request, $this->response, $this->asset->last_modified);
		}
	}

	public function action_embed()
	{
		$this->response->body(HTML::anchor('asset/view/'.$this->asset->id, "Download {$this->asset->title}"));
	}

	abstract public function action_view();

	abstract public function action_thumb();

	public function action_download()
	{
		$this->_log_download();
		$this->_do_download('download');
	}

	protected function _do_download($method = 'inline')
	{
		$this->response
			->headers(array(
				'Content-Type'				=>	$this->asset->get_mime(),
				'Content-Disposition'			=>	"$method; filename={$this->asset->filename}",
				'Content-Transfer-Encoding'	=>	'binary',
				'Content-Length'			=>	$this->asset->filesize,
				'Accept-Ranges'				=>	'bytes',
			))
			->body(readfile($this->asset->get_filename()));
	}

	protected function _log_download()
	{
		if ( ! $this->auth->logged_in())
		{
			$this->asset->log_download(Request::$client_ip);
		}
	}
}