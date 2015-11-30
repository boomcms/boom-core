<?php

namespace BoomCMS\Http\Controllers\CMS\Page\Version;

use BoomCMS\Core\Template;
use BoomCMS\Events;
use BoomCMS\Support\Facades\Template as TemplateFacade;
use DateTime;
use Illuminate\Support\Facades\Event;

class Save extends Version
{
    public function embargo()
    {
        parent::embargo();

        $time = $this->request->input('embargoed_until') ?
            strtotime($this->request->input('embargoed_until'))
            : time();

        $this->page->setEmbargoTime(new DateTime('@'.$time));

        $version = $this->page->getCurrentVersion();

        if ($version->isPublished()) {
            Event::fire(new Events\PageWasPublished($this->page, $this->auth->getPerson(), $version));
        } elseif ($version->isEmbargoed()) {
            Event::fire(new Events\PageWasEmbargoed($this->page, $this->auth->getPerson(), $version));
        }

        return $this->page->getCurrentVersion()->getStatus();
    }

    public function request_approval()
    {
        parent::request_approval();

        $this->page->markUpdatesAsPendingApproval();

        Event::fire(new Events\PageApprovalRequested($this->page, $this->auth->getPerson()));

        return $this->page->getCurrentVersion()->getStatus();
    }

    public function template(Template\Manager $manager)
    {
        parent::template($manager);

        $template = TemplateFacade::find($this->request->input('template_id'));
        $this->page->setTemplate($template);

        Event::fire(new Events\PageTemplateWasChanged($this->page, $template));

        return $this->page->getCurrentVersion()->getStatus();
    }

    public function title()
    {
        $oldTitle = $this->page->getTitle();
        $this->page->setTitle($this->request->input('title'));

        Event::fire(new Events\PageTitleWasChanged($this->page, $oldTitle, $this->page->getTitle()));

        return [
            'status'   => $this->page->getCurrentVersion()->getStatus(),
            'location' => (string) $this->page->url(true),
        ];
    }
}
