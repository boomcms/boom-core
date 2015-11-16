<?php

namespace BoomCMS\Core\Page;

use BoomCMS\Core\Template;
use BoomCMS\Support\Facades\Person;
use BoomCMS\Support\Facades\Template as TemplateFacade;
use BoomCMS\Support\Traits\HasId;
use DateTime;

class Version
{
    use HasId;

    /**
     * @var array
     */
    private $attributes;

    /**
     * @var Template\Template;
     */
    private $template;

    protected $editedBy;

    public function __construct(array $attributes)
    {
        if (isset($attributes['version:id'])) {
            $attributes['id'] = $attributes['version:id'];
            unset($attributes['version:id']);
        }

        $this->attributes = $attributes;
    }

    public function get($key)
    {
        return isset($this->attributes[$key]) ? $this->attributes[$key] : null;
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
        return $this->attributes['edited_time'] ?
            new DateTime('@'.$this->attributes['edited_time'])
            : null;
    }

    public function getEmbargoedUntil()
    {
        return (new DateTime())->setTimestamp($this->get('embargoed_until'));
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
            $this->template = TemplateFacade::findById($this->getTemplateId());
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
        return $this->attributes;
    }
}
