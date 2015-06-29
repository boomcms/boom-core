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

    public function save()
    {
        $input = $this->request->input();

        if (isset($input['template'])) {
            unset($input['template']);
        }

		$chunk = $this->provider->create($this->page, $input);

        if ($this->request->input('template')) {
            $chunk->template($this->request->input('template'));
        }

        // This is usually defined by the page controller.
        // We need to define a variant of it incase the callback is used in teh chunk view.
		View::share('chunk', function($type, $slotname, $page = null) {
			return Chunk::get($type, $slotname, $page);
		});

		return [
			'status' => $this->page->getCurrentVersion()->getStatus(),
			'html' => $chunk->render(),
		];
    }
}
