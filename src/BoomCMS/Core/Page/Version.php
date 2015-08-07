<?php

namespace BoomCMS\Core\Page;

use BoomCMS\Core\Template;
use BoomCMS\Support\Facades\Person;
use DateTime;

class Version
{
    /**
     * @var array
     */
    private $attrs;

    /**
     * @var Template\Template;
     */
    private $template;

    protected $editedBy;

    public function __construct(array $attrs)
    {
        if (isset($attrs['version:id'])) {
            $attrs['id'] = $attrs['version:id'];
            unset($attrs['version:id']);
        }

        $this->attrs = $attrs;
    }

    public function get($key)
    {
        return isset($this->attrs[$key]) ? $this->attrs[$key] : null;
    }

    public function getEditedBy()
    {
        if ($this->editedBy === null) {
            $this->editedBy = Person::findById($this->get('edited_by'));
        }

        return $this->editedBy;
    }

    public function getEditedTime()
    {
        return $this->attrs['edited_time'] ?
            new DateTime('@'.$this->attrs['edited_time'])
            : null;
    }

    public function getEmbargoedUntil()
    {
        return (new DateTime())->setTimestamp($this->get('embargoed_until'));
    }

    public function getId()
    {
        return $this->get('id');
    }

    public function getPageId()
    {
        return $this->get('page_id');
    }

    public function getStatus()
    {
        return $this->status();
    }

    public function getTemplateId()
    {
        return $this->get('template_id');
    }

    public function getTemplate()
    {
        if ($this->template === null) {
            $provider = new Template\Provider();
            $this->template = $provider->findById($this->getTemplateId());
        }

        return $this->template;
    }

    public function getTitle()
    {
        return $this->get('title');
    }

    public function isDraft()
    {
        return $this->get('embargoed_until') === null;
    }

    public function isEmbargoed()
    {
        return $this->get('embargoed_until') > time();
    }

    public function isPendingApproval()
    {
        return $this->get('pending_approval') == true;
    }

    public function isPublished()
    {
        return $this->get('embargoed_until') && $this->get('embargoed_until') <= time();
    }

    /**
     * Returns the status of the current page version.
     *
     *
     * @return string
     */
    public function status()
    {
        if ($this->isPendingApproval()) {
            return 'pending approval';
        } elseif ($this->isDraft()) {
            return 'draft';
        } elseif ($this->isPublished()) {
            return 'published';
        } elseif ($this->isEmbargoed()) {
            return 'embargoed';
        }
    }

    public function toArray()
    {
        return $this->attrs;
    }
}
