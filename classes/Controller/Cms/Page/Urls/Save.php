<?php

class Controller_Cms_Page_Urls_Save extends Controller_Cms_Page_Urls
{
	public function before()
	{
		parent::before();

		$this->_csrf_check();
	}

	public function action_add()
	{
		$location = \Boom\Page\URL::sanitise($this->request->post('location'));

		$this->page_url->where('location', '=', $location)->find();

		if ($this->page_url->loaded() && $this->page_url->page_id !== $this->page->getId())
		{
			// Url is being used for a different page.
			// Notify that the url is already in use so that the JS can load a prompt to move the url.
			$this->response->body(json_encode(array('existing_url_id' => $this->page_url->id)));
		}
		elseif ( ! $this->page_url->loaded())
		{
			//  It's not an old URL, so create a new one.
			$this->page_url
				->values(array(
					'location'		=>	$location,
					'page_id'		=>	$this->page->getId(),
					'is_primary'	=>	false,
				))
				->create();

			$this->log("Added secondary url $location to page " . $this->page->getTitle() . "(ID: " . $this->page->getId() . ")");
		}
	}

	public function action_delete()
	{
		if ( ! $this->page_url->is_primary)
		{
			$this->page_url->delete();
		}
	}

	public function action_make_primary()
	{
		$this->page_url->make_primary();
	}

	public function action_move()
	{
		$this->page_url->values(array(
			'page_id'		=>	$this->page->getId(),
			'is_primary'	=>	false, // Make sure that it's only a secondary url for the this page.
		))
		->update();
	}
}
