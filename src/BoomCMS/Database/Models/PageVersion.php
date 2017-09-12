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
    const ATTR_CREATED_BY = 'created_by';
    const ATTR_CREATED_AT = 'created_at';
    const ATTR_EMBARGOED_UNTIL = 'embargoed_until';
    const ATTR_PENDING_APPROVAL = 'pending_approval';
    const ATTR_CHUNK_TYPE = 'chunk_type';
    const ATTR_CHUNK_ID = 'chunk_id';
    const ATTR_RESTORED_FROM = 'restored_from';

    protected $casts = [
        self::ATTR_PAGE             => 'integer',
        self::ATTR_PENDING_APPROVAL => 'boolean',
        self::ATTR_CHUNK_ID         => 'integer',
        self::ATTR_RESTORED_FROM    => 'integer',
        self::ATTR_TITLE            => 'string',
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
        return $this->belongsTo(Person::class, 'created_by');
    }

    /**
     * @return int
     */
    public function getChunkId()
    {
        return $this->{self::ATTR_CHUNK_ID};
    }

    /**
     * Returns the value of the chunk_type field.
     *
     * @return string
     */
    public function getChunkType()
    {
        return $this->{self::ATTR_CHUNK_TYPE};
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
        return (new DateTime())->setTimestamp($this->{self::ATTR_CREATED_AT});
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
            ->where(self::ATTR_CREATED_AT, '>', $this->getEditedTime()->getTimestamp())
            ->orderBy(self::ATTR_CREATED_AT, 'asc')
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
            ->where(self::ATTR_CREATED_AT, '<', $this->getEditedTime()->getTimestamp())
            ->orderBy(self::ATTR_CREATED_AT, 'desc')
            ->first();
    }

    /**
     * @return int
     */
    public function getRestoredVersionId()
    {
        return $this->{self::ATTR_RESTORED_FROM};
    }

    /**
     * @param null|DateTime $time
     *
     * @return string
     */
    public function getStatus(DateTime $time = null)
    {
        return $this->status($time);
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

    public function getTitle(): string
    {
        return (string) $this->{self::ATTR_TITLE};
    }

    /**
     * Whether this version relates to a content change.
     *
     * @return bool
     */
    public function isContentChange()
    {
        return !empty($this->{self::ATTR_CHUNK_TYPE})
            && !empty($this->{self::ATTR_CHUNK_ID});
    }

    /**
     * @return bool
     */
    public function isDraft()
    {
        return $this->{self::ATTR_EMBARGOED_UNTIL} === null;
    }

    /**
     * Whether the version is embargoed.
     *
     * If a time is given then the embargo time is compared with the given time.
     *
     * Otherwise it is compared with the current time.
     *
     * @param null|DateTime $time
     *
     * @return bool
     */
    public function isEmbargoed(DateTime $time = null)
    {
        $timestamp = $time ? $time->getTimestamp() : time();

        return $this->{self::ATTR_EMBARGOED_UNTIL} > $timestamp;
    }

    /**
     * @return bool
     */
    public function isPendingApproval()
    {
        return $this->{self::ATTR_PENDING_APPROVAL} === true;
    }

    /**
     * Whether the version is published.
     *
     * If a time is given then the embargo time is compared with the given time.
     *
     * Otherwise it is compared with the current time.
     *
     * @param null|DateTime $time
     *
     * @return bool
     */
    public function isPublished(DateTime $time = null)
    {
        $timestamp = $time ? $time->getTimestamp() : time();

        return $this->{self::ATTR_EMBARGOED_UNTIL} && $this->{self::ATTR_EMBARGOED_UNTIL} <= $timestamp;
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

    /**
     * Mark the version as being restored from another.
     *
     * @param PageVersionInterface $version
     *
     * @return $this
     */
    public function setRestoredFrom(PageVersionInterface $version)
    {
        $this->{self::ATTR_RESTORED_FROM} = $version->getId();

        return $this;
    }

    public function scopeLastPublished($query)
    {
        // Get the published version with the most recent embargoed time.
        // Order by ID as well incase there's multiple versions with the same embargoed time.
        return $query
            ->whereNotNull(self::ATTR_EMBARGOED_UNTIL)
            ->where(self::ATTR_EMBARGOED_UNTIL, '<=', time())
            ->orderBy(self::ATTR_CREATED_AT, 'desc')
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
                ->where(self::ATTR_CREATED_AT, '<=', Editor::getTime()->getTimestamp())
                ->orderBy(self::ATTR_CREATED_AT, 'desc')
                ->orderBy(self::ATTR_ID, 'desc');
        }

        return (Editor::isDisabled()) ?
                $this->scopeLastPublished($query)
                : $query->orderBy(self::ATTR_CREATED_AT, 'desc');
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
     * If a time parameter is given then the status of the page at that time will be returned.
     *
     * @param null|DateTime $time
     *
     * @return string
     */
    public function status(DateTime $time = null)
    {
        if ($this->isPendingApproval()) {
            return 'pending approval';
        } elseif ($this->isDraft()) {
            return 'draft';
        } elseif ($this->isPublished($time)) {
            return 'published';
        } elseif ($this->isEmbargoed($time)) {
            return 'embargoed';
        }
    }

    public function template()
    {
        return $this->belongsTo(Template::class);
    }
}
