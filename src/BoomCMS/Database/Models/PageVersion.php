<?php

namespace BoomCMS\Database\Models;

use BoomCMS\Contracts\Models\Page as PageInterface;
use BoomCMS\Contracts\Models\PageVersion as PageVersionInterface;
use BoomCMS\Contracts\Models\Person as PersonInterface;
use BoomCMS\Contracts\Models\Template as TemplateInterface;
use BoomCMS\Foundation\Database\Model;
use BoomCMS\Support\Facades\Editor;
use DateTime;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class PageVersion extends Model implements PageVersionInterface
{
    const ATTR_PAGE = 'page_id';
    const ATTR_TEMPLATE = 'template_id';
    const ATTR_TITLE = 'title';
    const ATTR_EDITED_BY = 'edited_by';
    const ATTR_EDITED_AT = 'edited_time';
    const ATTR_EMBARGOED_UNTIL = 'embargoed_until';
    const ATTR_PENDING_APPROVAL = 'pending_approval';

    protected $casts = [
        self::ATTR_PAGE             => 'integer',
        self::ATTR_PENDING_APPROVAL => 'boolean',
    ];

    protected $table = 'page_versions';

    /**
     * @var Template;
     */
    private $template;

    public function __construct(array $attributes = [])
    {
        if (isset($attributes['version:id'])) {
            $attributes['id'] = $attributes['version:id'];
            unset($attributes['version:id']);
        }

        parent::__construct($attributes);
    }

    public function editedBy()
    {
        return $this->belongsTo(Person::class, 'edited_by');
    }

    /**
     * @return PersonInterface
     */
    public function getEditedBy()
    {
        return $this->editedBy;
    }

    /**
     * @return DateTime
     */
    public function getEditedTime()
    {
        return (new DateTime())->setTimestamp($this->{self::ATTR_EDITED_AT});
    }

    /**
     * @return DateTime
     */
    public function getEmbargoedUntil()
    {
        return (new DateTime())->setTimestamp($this->{self::ATTR_EMBARGOED_UNTIL});
    }

    /**
     * Returns the next version.
     *
     * @return PageVersion
     */
    public function getNext()
    {
        return $this
            ->where(self::ATTR_PAGE, $this->getPageId())
            ->where(self::ATTR_EDITED_AT, '>', $this->getEditedTime()->getTimestamp())
            ->orderBy(self::ATTR_EDITED_AT, 'asc')
            ->first();
    }

    /**
     * @return int
     */
    public function getPageId()
    {
        return $this->{self::ATTR_PAGE};
    }

    /**
     * Returns the previous version.
     *
     * @return PageVersion
     */
    public function getPrevious()
    {
        return $this
            ->where(self::ATTR_PAGE, $this->getPageId())
            ->where(self::ATTR_EDITED_AT, '<', $this->getEditedTime()->getTimestamp())
            ->orderBy(self::ATTR_EDITED_AT, 'desc')
            ->first();
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status();
    }

    /**
     * @return int
     */
    public function getTemplateId()
    {
        return $this->{self::ATTR_TEMPLATE};
    }

    /**
     * @return TemplateInterface
     */
    public function getTemplate()
    {
        if ($this->template === null) {
            $template = $this->template()->first();
            $this->template = $template ?: new Template();
        }

        return $this->template;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->{self::ATTR_TITLE};
    }

    /**
     * @return bool
     */
    public function isDraft()
    {
        return $this->{self::ATTR_EMBARGOED_UNTIL} === null;
    }

    /**
     * @return bool
     */
    public function isEmbargoed()
    {
        return $this->{self::ATTR_EMBARGOED_UNTIL} > time();
    }

    /**
     * @return bool
     */
    public function isPendingApproval()
    {
        return $this->{self::ATTR_PENDING_APPROVAL} === true;
    }

    /**
     * @return bool
     */
    public function isPublished()
    {
        return $this->{self::ATTR_EMBARGOED_UNTIL} && $this->{self::ATTR_EMBARGOED_UNTIL} <= time();
    }

    /**
     * Unpublish the version and make it a draft.
     *
     * @return $this
     */
    public function makeDraft()
    {
        $this->{self::ATTR_EMBARGOED_UNTIL} = null;

        return $this;
    }

    /**
     * Set the time when the version was created.
     *
     * @param DateTime $time
     *
     * @return $this
     */
    public function setEditedAt(DateTime $time)
    {
        $this->{self::ATTR_EDITED_AT} = $time->getTimestamp();

        return $this;
    }

    /**
     * Set the user who created the page version.
     *
     * This can be set to null to allow page content to be changed programmatically,
     * when a real user may not be logged in.
     *
     * @param null|PersonInterface $person
     *
     * @return $this
     */
    public function setEditedBy(PersonInterface $person = null)
    {
        $this->{self::ATTR_EDITED_BY} = ($person === null) ? null : $person->getId();

        return $this;
    }

    /**
     * Set the page that the version belongs to.
     *
     * @param PageInterface $page
     *
     * @return $this
     */
    public function setPage(PageInterface $page)
    {
        $this->{self::ATTR_PAGE} = $page->getId();

        return $this;
    }

    public function scopeLastPublished($query)
    {
        // Get the published version with the most recent embargoed time.
        // Order by ID as well incase there's multiple versions with the same embargoed time.
        return $query
            ->whereNotNull(self::ATTR_EMBARGOED_UNTIL)
            ->where(self::ATTR_EMBARGOED_UNTIL, '<=', time())
            ->orderBy(self::ATTR_EDITED_AT, 'desc')
            ->orderBy(self::ATTR_ID, 'desc');
    }

    /**
     * @param QueryBuilder $query
     *
     * @return QueryBuilder
     */
    public function scopeLatestAvailable(QueryBuilder $query)
    {
        if (Editor::isHistory()) {
            return $query
                ->where(self::ATTR_EDITED_AT, '<=', Editor::getTime()->getTimestamp())
                ->orderBy(self::ATTR_EDITED_AT, 'desc')
                ->orderBy(self::ATTR_ID, 'desc');
        }

        return (Editor::isDisabled()) ?
                $this->scopeLastPublished($query)
                : $query->orderBy(self::ATTR_EDITED_AT, 'desc');
    }

    public function scopeForPage($query, PageInterface $page)
    {
        return $query->where(self::ATTR_PAGE, '=', $page->getId());
    }

    public function setTitleAttribute($title)
    {
        $title = trim(html_entity_decode(strip_tags($title)));

        if (strlen($title) <= 100) {
            $this->attributes[self::ATTR_TITLE] = $title;
        }
    }

    /**
     * Returns the status of the current page version.
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

    public function template()
    {
        return $this->belongsTo(Template::class);
    }
}
