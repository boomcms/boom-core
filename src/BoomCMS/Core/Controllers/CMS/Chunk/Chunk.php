<?php

namespace BoomCMS\Core\Controllers\CMS\Chunk;

use BoomCMS\Core\Controllers\Controller;
use BoomCMS\Core\Auth\Auth;
use BoomCMS\Core\Chunk\Provider;
use BoomCMS\Core\Page\Page;
use Illuminate\Http\Request;

class Chunk extends Controller
{
	/**
	 *
	 * @var Page
	 */
	protected $page;
	
	/**
	 *
	 * @var Provider
	 */
	protected $provider;

    public function __construct(Auth $auth, Request $request, Provider $provider)
    {
        $this->auth = $auth;
        $this->request = $request;
        $this->page = $this->request->route()->getParameter('page');
		$this->provider = $provider;
		
		$this->page->wasCreatedBy($this->auth->getPerson()) || 
			parent::authorization('edit_page_content', $this->page);
    }

    public function remove()
    {
        $this->authCheck();
        $this->_createVersion();

        $this->_send_response($this->_preview_default_chunk());
    }

    public function save()
    {
		$chunk = $this->provider->create($this->page, $this->request->input());
		
		return [
			'status' => $this->page->getCurrentVersion()->getStatus(),
			'html' => $chunk->render(),
		];
    }

    protected function _preview_chunk() {}

    protected function _save_chunk()
    {
        return $this->_model = ORM::factory("Chunk_".ucfirst($this->_type))
            ->values($this->request->input())
            ->set('page_vid', $this->_new_version->id)
            ->create();
    }
}
