<?php

namespace BoomCMS\Http\Controllers\CMS\Page\Version;

use BoomCMS\Core\Commands\CreatePagePrimaryUri;
use BoomCMS\Core\Template;
use Illuminate\Support\Facades\Bus;

class Save extends Version
{
    public function embargo()
    {
        parent::embargo();

        $embargoed_until = $this->request->input('embargoed_until') ?
            strtotime($this->request->input('embargoed_until'))
            : time();

        $this->page->setEmbargoTime($embargoed_until);

        return $this->page->getCurrentVersion()->getStatus();
    }

    public function request_approval()
    {
        parent::request_approval();

        $this->page->makeUpdatesAsPendingApproval();

        return $this->page->getCurrentVersion()->getStatus();
    }

    public function template(Template\Manager $manager)
    {
        parent::template($manager);

        $this->page->setTemplateId($this->request->input('template_id'));

        return $this->page->getCurrentVersion()->getStatus();
    }

    public function title()
    {
        $oldTitle = $this->page->getTitle();
        $this->page->setTitle($this->request->input('title'));

        if ($oldTitle !== $this->page->getTitle()
            && $oldTitle == 'Untitled'
            && $this->page->url()->getLocation() !== '/'
        ) {
            $prefix = ($this->page->getParent()->getChildPageUrlPrefix()) ?: $this->page->getParent()->url()->getLocation();

            $url = Bus::dispatch(
                new CreatePagePrimaryUri(
                    $this->provider,
                    $this->page,
                    $prefix
                )
            );

            return [
                'status'   => $this->page->getCurrentVersion()->getStatus(),
                'location' => (string) $url,
            ];
        }

        return $this->page->getCurrentVersion()->getStatus();
    }
}
