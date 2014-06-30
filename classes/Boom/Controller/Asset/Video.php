<?php

class Boom_Controller_Asset_Video extends Controller_Asset
{
	public $enable_caching = false;

	public function action_view()
	{
		$this->_log_download();
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mime = finfo_file($finfo, $this->asset->getFilename());
		finfo_close($finfo);

		$this->response
			->send_file($this->asset->getFilename(), $this->asset->filename, array(
				'inline'		=>	true,
				'mime_type'	=>	$mime,
				'resumable'	=>	true,
			));
	}

	public function action_thumb()
	{
		$filename = ($this->asset->thumbnail_asset_id)?
			$this->asset->thumbnail->getFilename() :
			MODPATH.'boom/media/boom/img/icons/40x40/mov_icon.gif';

		$image = Image::factory($filename)
			->resize($this->request->param('width'), $this->request->param('height'));

		$this->response
			->headers('Content-type', $image->mime)
			->body($image->render());
	}
}