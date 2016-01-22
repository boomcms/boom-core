<?php

namespace BoomCMS\Http\Controllers\Page\Version;

use BoomCMS\Events;
use BoomCMS\Support\Facades\Template as TemplateFacade;
use DateTime;
use Illuminate\Support\Facades\Event;

class Save extends Version
{
    public function template()
    {
        parent::template();

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
