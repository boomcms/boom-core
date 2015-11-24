<?php

namespace BoomCMS\Http\Controllers\CMS\Page\Urls;

use BoomCMS\Jobs\MakeURLPrimary;
use BoomCMS\Support\Facades\URL;
use Illuminate\Support\Facades\Bus;

class Save extends BaseController
{
    public function add()
    {
        $location = $this->request->input('location');
        $this->url = URL::findByLocation($location);

        if ($this->url && !$this->url->isForPage($this->page)) {
            // Url is being used for a different page.
            // Notify that the url is already in use so that the JS can load a prompt to move the url.
            return ['existing_url_id' => $this->url->getId()];
        } elseif (!$this->url) {
            URL::create($location, $this->page->getId());
        }
    }

    public function delete()
    {
        if (!$this->url->isPrimary()) {
            URL::delete($this->url);
        }
    }

    public function makePrimary()
    {
        Bus::dispatch(new MakeURLPrimary($this->url));
    }

    public function move()
    {
        $this->url
            ->setPageId($this->page->getId())
            ->setIsPrimary(false);

        URL::save($this->url);
    }
}
