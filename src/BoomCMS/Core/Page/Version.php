<?php

namespace BoomCMS\Core\Page;

use BoomCMS\Core\Template;
use DateTime;

class Version
{
    /**
     *
     * @var array
     */
    private $attrs;

    /**
     *
     * @var Template\Template;
     */
    private $template;

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

    public function getEditedTime()
    {
        return $this->attrs['edited_time']?
            new DateTime('@' . $this->attrs['edited_time'])
            : null;
    }

	public function getEmbargoedUntil()
	{
		return $this->get('embargoed_until');
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

    public function isPendingApproval()
    {
        return $this->get('pending_approval') == true;
    }

    public function isPublished()
    {
        return $this->get('embargoed_until') && $this->get('embargoed_until') < time();
    }

    /**
     * Returns the status of the current page version.
     *
     * Status could be:
     *
     * * 'published' if the version is published.
     * * 'embargoed' if the version is published but won't become live until a future time.
     * * 'draft' if it's not published.
     *
     * @return string
     */
    public function status()
    {
        if ($this->isPendingApproval()) {
            return 'pending approval';
        } elseif ($this->get('embargoed_until') === null) {
            // Version is a draft if an embargo time hasn't been set.
            return 'draft';
        } elseif ($this->get('embargoed_until') <= time()) {
            // Version is live if the embargo time is in the past.
            return 'published';
        } elseif ($this->get('embargoed_until') > time()) {
            // Version is embargoed if the embargo time is in the future.
            return 'embargoed';
        }
    }

    public function toArray()
    {
        return $this->attrs;
    }
}