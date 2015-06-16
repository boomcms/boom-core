<?php

namespace BoomCMS\Core\Controllers\CMS\Page\Version;

use BoomCMS\Core\Template;
use BoomCMS\Core\URL\Helpers as URL;

class Save extends Version
{
    public function embargo()
    {
        parent::embargo();

        $embargoed_until = $this->request->input('embargoed_until') ? strtotime($this->request->input('embargoed_until')) : $_SERVER['REQUEST_TIME'];

        $this->newVersion
            ->set('pending_approval', false)
            ->create()
            ->embargo($embargoed_until)
            ->copy_chunks($this->oldVersion);

        if ($this->page->getCurrentVersion()->isPublished()) {
            $commander = new \Boom\Page\Commander($this);

            return $commander
                ->addCommand(new \Boom\Page\Command\Delete\Drafts())
                ->execute();
        }
    }

    public function request_approval()
    {
        parent::request_approval();

        $this->page->makeUpdatesAsPendingApproval();
    }

    public function template(Template\Manager $manager)
    {
        parent::template();

        $this->page->setTemplateId($this->request->input('template_id'));
    }

    public function title()
    {
        $oldTitle = $this->page->getTitle();

        $this->page->setTitle($this->request->input('title'));

        if ($oldTitle !== $this->page->getTitle()
            && $oldTitle == 'Untitled'
            && ! $this->page->url()->location === '/'
        ) {
//            $location = URL::fromTitle($this->page->parent()->url()->location, $this->page->getTitle());
//            $url = \Boom\Page\URL::createPrimary($location, $this->page->getId());
//
//            // Put the page's new URL in the response body so that the JS will redirect to the new URL.
//            return[
//                'location' => (string) $url,
//            ];
        }
    }
}
