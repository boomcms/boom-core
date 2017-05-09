<?php

namespace BoomCMS\Database\Models;

use BoomCMS\Contracts\Models\Page as PageInterface;
use BoomCMS\Contracts\Models\PageVersion as PageVersionInterface;
use BoomCMS\Contracts\Models\Tag as TagInterface;
use BoomCMS\Contracts\Models\Template as TemplateInterface;
use BoomCMS\Contracts\Models\URL as URLInterface;
use BoomCMS\Contracts\LinkableInterface;
use BoomCMS\Contracts\SingleSiteInterface;
use BoomCMS\Foundation\Database\Model;
use BoomCMS\Support\Facades\Editor;
use BoomCMS\Support\Helpers\URL as URLHelper;
use BoomCMS\Support\Traits\HasCreatedBy;
use BoomCMS\Support\Traits\HasFeatureImage;
use BoomCMS\Support\Traits\SingleSite;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Page extends Model implements PageInterface, LinkableInterface, SingleSiteInterface
{
    use HasCreatedBy;
    use HasFeatureImage;
    use SingleSite;
    use SoftDeletes;

    const ATTR_SEQUENCE = 'sequence';
    const ATTR_VISIBLE = 'visible';
    const ATTR_VISIBLE_FROM = 'visible_from';
    const ATTR_VISIBLE_TO = 'visible_to';
    const ATTR_INTERNAL_NAME = 'internal_name';
    const ATTR_INTERNAL_INDEXING = 'internal_indexing';
    const ATTR_EXTERNAL_INDEXING = 'external_indexing';
    const ATTR_VISIBLE_IN_NAV = 'visible_in_nav';
    const ATTR_VISIBLE_IN_NAV_CMS = 'visible_in_nav_cms';
    const ATTR_CHILDREN_VISIBLE_IN_NAV = 'children_visible_in_nav';
    const ATTR_CHILDREN_VISIBLE_IN_NAV_CMS = 'children_visible_in_nav_cms';
    const ATTR_CHILD_TEMPLATE = 'children_template_id';
    const ATTR_CHILD_URL_PREFIX = 'children_url_prefix';
    const ATTR_CHILD_ORDERING_POLICY = 'children_ordering_policy';
    const ATTR_GRANDCHILD_TEMPLATE = 'grandchild_template_id';
    const ATTR_KEYWORDS = 'keywords';
    const ATTR_DESCRIPTION = 'description';
    const ATTR_CREATED_BY = 'created_by';
    const ATTR_CREATED_AT = 'created_at';
    const ATTR_PRIMARY_URI = 'primary_uri';
    const ATTR_FEATURE_IMAGE = 'feature_image_id';
    const ATTR_PARENT = 'parent_id';
    const ATTR_DELETED_AT = 'deleted_at';
    const ATTR_DELETED_BY = 'deleted_by';
    const ATTR_DISABLE_DELETE = 'disable_delete';
    const ATTR_ADD_BEHAVIOUR = 'add_behaviour';
    const ATTR_CHILD_ADD_BEHAVIOUR = 'child_add_behaviour';
    const ATTR_ENABLE_ACL = 'enable_acl';

    const DEFAULT_TITLE = 'Untitled';

    const ORDER_SEQUENCE = 1;
    const ORDER_TITLE = 2;
    const ORDER_VISIBLE_FROM = 4;
    const ORDER_ASC = 8;
    const ORDER_DESC = 16;

    /**
     * Values for the 'add_behaviour' and 'child_add_behaviour' columns.
     *
     * These columns store the behaviour of the add page button when on the page / its children
     */
    const ADD_PAGE_NONE = 1;
    const ADD_PAGE_CHILD = 2;
    const ADD_PAGE_SIBLING = 3;

    protected $appends = ['has_children', 'url', 'visible'];

    protected $casts = [
        self::ATTR_ADD_BEHAVIOUR               => 'integer',
        self::ATTR_CHILD_ADD_BEHAVIOUR         => 'integer',
        self::ATTR_CHILD_TEMPLATE              => 'integer',
        self::ATTR_CHILDREN_VISIBLE_IN_NAV     => 'boolean',
        self::ATTR_CHILDREN_VISIBLE_IN_NAV_CMS => 'boolean',
        self::ATTR_DESCRIPTION                 => 'string',
        self::ATTR_DISABLE_DELETE              => 'boolean',
        self::ATTR_EXTERNAL_INDEXING           => 'boolean',
        self::ATTR_GRANDCHILD_TEMPLATE         => 'integer',
        self::ATTR_ID                          => 'integer',
        self::ATTR_INTERNAL_INDEXING           => 'boolean',
        self::ATTR_PARENT                      => 'integer',
        self::ATTR_VISIBLE_IN_NAV              => 'boolean',
        self::ATTR_VISIBLE_IN_NAV_CMS          => 'boolean',
        self::ATTR_VISIBLE                     => 'boolean',
        self::ATTR_ENABLE_ACL                  => 'boolean',
        self::ATTR_FEATURE_IMAGE               => 'integer',
    ];

    /**
     * @var PageVersion
     */
    protected $currentVersion;

    protected $table = 'pages';

    /**
     * @var Asset
     */
    protected $featureImage;

    /**
     * @var URLInterface
     */
    protected $primaryUrl;

    /**
     * @return bool
     */
    public function aclEnabled()
    {
        return $this->{self::ATTR_ENABLE_ACL} === true;
    }

    /**
     * @param int $groupId
     *
     * @return $this
     */
    public function addAclGroupId($groupId)
    {
        try {
            DB::table('page_acl')
                ->insert([
                    'page_id'  => $this->getId(),
                    'group_id' => $groupId,
                ]);
        } catch (QueryException $e) {
        }

        return $this;
    }

    public function addRelation(PageInterface $page)
    {
        $this->relations()->attach($page, [
            'created_at'  => time(),
            'created_by'  => Auth::user()->getId(),
        ]);

        return $this;
    }

    public function addTag(TagInterface $tag)
    {
        $this->tags()->attach($tag);

        return $this;
    }

    /**
     * Adds a new version to the page.
     *
     * If the current version is embargoed then the new version is also embargoed.
     * If the current version is published then the new version becomes a draft.
     *
     * @param array $attrs
     *
     * @return PageVersion
     */
    public function addVersion(array $attrs = [])
    {
        if ($oldVersion = $this->getCurrentVersion()) {
            $attrs += $oldVersion->toArray();
        }

        // Chunk type and ID fields shouldn't be copied.
        unset($attrs[PageVersion::ATTR_CHUNK_TYPE]);
        unset($attrs[PageVersion::ATTR_CHUNK_ID]);
        unset($attrs[PageVersion::ATTR_RESTORED_FROM]);

        $newVersion = new PageVersion($attrs);
        $newVersion->setPage($this);

        /*
         * Only make the new version a draft if the old version is published.
         * When a new page is created we set the edited_at time to make the first version published
         */
        if ($oldVersion->isPublished()) {
            $newVersion->makeDraft();
        }

        $newVersion->save();

        $this->setCurrentVersion($newVersion);

        return $this->getCurrentVersion();
    }

    /**
     * @return bool
     */
    public function allowsExternalIndexing()
    {
        return $this->{self::ATTR_EXTERNAL_INDEXING};
    }

    /**
     * @return bool
     */
    public function allowsInternalIndexing()
    {
        return $this->{self::ATTR_INTERNAL_INDEXING};
    }

    /**
     * @return bool
     */
    public function canBeDeleted()
    {
        return $this->{self::ATTR_DISABLE_DELETE} === false;
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * @return bool
     */
    public function childrenAreVisibleInNav()
    {
        return $this->{self::ATTR_CHILDREN_VISIBLE_IN_NAV};
    }

    /**
     * @return bool
     */
    public function childrenAreVisibleInCmsNav()
    {
        return $this->{self::ATTR_CHILDREN_VISIBLE_IN_NAV_CMS};
    }

    /**
     * @return int
     */
    public function countChildren()
    {
        return $this->children->count();
    }

    /**
     * Returns an array of IDs for groups which can view this page.
     *
     * @return array
     */
    public function getAclGroupIds()
    {
        return DB::table('page_acl')
            ->select('group_id')
            ->where('page_id', $this->getId())
            ->pluck('group_id')
            ->all();
    }

    /**
     * @return int
     */
    public function getAddPageBehaviour()
    {
        return $this->{self::ATTR_ADD_BEHAVIOUR} ?: self::ADD_PAGE_NONE;
    }

    /**
     * @return Page
     */
    public function getAddPageParent()
    {
        $behaviour = $this->{self::ATTR_ADD_BEHAVIOUR};

        if ($behaviour === self::ADD_PAGE_NONE && !$this->isRoot()) {
            $behaviour = $this->getParent()->getChildAddPageBehaviour();
        }

        if ($behaviour === self::ADD_PAGE_SIBLING && !$this->isRoot()) {
            return $this->getParent();
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getChildAddPageBehaviour()
    {
        return $this->{self::ATTR_CHILD_ADD_BEHAVIOUR} ?: self::ADD_PAGE_NONE;
    }

    /**
     * Returns an array of [column, direction] indicating the page's child ordering policy.
     *
     * @return array
     */
    public function getChildOrderingPolicy()
    {
        $order = $this->{self::ATTR_CHILD_ORDERING_POLICY};
        $column = 'sequence';

        if ($order & static::ORDER_TITLE) {
            $column = 'title';
        } elseif ($order & static::ORDER_VISIBLE_FROM) {
            $column = 'visible_from';
        }

        $direction = ($order & static::ORDER_ASC) ? 'asc' : 'desc';

        return [$column, $direction];
    }

    public function getChildPageUrlPrefix()
    {
        return $this->{self::ATTR_CHILD_URL_PREFIX};
    }

    /**
     * @return DateTime
     */
    public function getCreatedTime()
    {
        return (new DateTime())->setTimestamp($this->{self::ATTR_CREATED_AT});
    }

    public function getCurrentVersion()
    {
        if ($this->currentVersion === null) {
            $version = PageVersion::forPage($this)
                ->latestAvailable()
                ->first();

            $this->currentVersion = $version ? $version : new PageVersion();
        }

        return $this->currentVersion;
    }

    /**
     * Get the description property for the page.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->{self::ATTR_DESCRIPTION};
    }

    /**
     * Returns the default template ID that child pages should use.
     *
     * @return int
     */
    public function getDefaultChildTemplateId()
    {
        if ($templateId = $this->{self::ATTR_CHILD_TEMPLATE}) {
            return $templateId;
        }

        $parent = $this->getParent();

        return ($parent && !empty($parent->getGrandchildTemplateId())) ?
            $parent->getGrandchildTemplateId()
            : $this->getTemplateId();
    }

    /**
     * If a default grandchild template ID is set then that is returned.
     *
     * Otherwise the current template ID of this page is returned.
     *
     * @return int
     */
    public function getDefaultGrandchildTemplateId()
    {
        $grandchildTemplateId = $this->getGrandchildTemplateId();

        return empty($grandchildTemplateId) ? $this->getTemplateId() : $grandchildTemplateId;
    }

    public function getGrandchildTemplateId()
    {
        return $this->{self::ATTR_GRANDCHILD_TEMPLATE};
    }

    /**
     * Returns the has_children attribute for the JSON form.
     *
     * @return int
     */
    public function getHasChildrenAttribute()
    {
        return (bool) $this->hasChildren();
    }

    /**
     * Returns the url attribute for the JSON form.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return (string) $this->url();
    }

    /**
     * Returns the visible attribute for the JSON form.
     *
     * @return int
     */
    public function getVisibleAttribute()
    {
        return (int) $this->isVisible();
    }

    public function getInternalName()
    {
        return $this->{self::ATTR_INTERNAL_NAME};
    }

    /**
     * @return string
     */
    public function getKeywords()
    {
        return $this->{self::ATTR_KEYWORDS};
    }

    /**
     * @return DateTime
     */
    public function getLastModified()
    {
        return $this->getCurrentVersion()->getEditedTime();
    }

    /**
     * Returns the last published version for the page.
     *
     * @return PageVersionInterface
     */
    public function getLastPublished()
    {
        return PageVersion::forPage($this)->lastPublished()->first();
    }

    public function getManualOrderPosition()
    {
        return $this->{self::ATTR_SEQUENCE};
    }

    /**
     * @return null|PageInterface
     */
    public function getParent()
    {
        return $this->isRoot() ? null : $this->parent;
    }

    /**
     * @return int
     */
    public function getParentId()
    {
        return $this->{self::ATTR_PARENT};
    }

    /**
     * @return TemplateInterface
     */
    public function getTemplate()
    {
        return $this->getCurrentVersion()->getTemplate();
    }

    public function getTemplateId()
    {
        return $this->getCurrentVersion()->getTemplateId();
    }

    public function getTitle(): string
    {
        return $this->getCurrentVersion()->getTitle();
    }

    public function getUrls()
    {
        return $this->hasMany(URL::class)->orderBy(URL::ATTR_LOCATION, 'asc')->get();
    }

    /**
     * @return DateTime
     */
    public function getVisibleFrom()
    {
        $timestamp = $this->{self::ATTR_VISIBLE_FROM} ?: time();

        return new DateTime('@'.$timestamp);
    }

    /**
     * Returns the visible to date, or null if none is set.
     *
     * @return null|DateTime
     */
    public function getVisibleTo()
    {
        return empty($this->{self::ATTR_VISIBLE_TO}) ? null : new DateTime('@'.$this->{self::ATTR_VISIBLE_TO});
    }

    /**
     * @return bool
     */
    public function hasChildren()
    {
        return $this->children()->exists();
    }

    /**
     * Whether the page has been deleted.
     *
     * @return bool
     */
    public function isDeleted()
    {
        return !empty($this->{self::ATTR_DELETED_AT});
    }

    /**
     * Returns whether this page is the parent of a given page.
     *
     * @param PageInterface $page
     *
     * @return bool
     */
    public function isParentOf(PageInterface $page)
    {
        return $page->getParentId() === $this->getId();
    }

    public function isRoot()
    {
        return $this->getParentId() === null;
    }

    public function isVisible()
    {
        return $this->isVisibleAtTime(new DateTime('now'));
    }

    /**
     * @return bool
     */
    public function isVisibleAtAnyTime()
    {
        return isset($this->attributes[self::ATTR_VISIBLE])
            && (bool) $this->attributes[self::ATTR_VISIBLE] === true;
    }

    /**
     * @param DateTime $time
     *
     * @return bool
     */
    public function isVisibleAtTime(DateTime $time)
    {
        return $this->isVisibleAtAnyTime() &&
            $this->getVisibleFrom()->getTimestamp() <= $time->getTimestamp() &&
            ($this->getVisibleTo() === null || $this->getVisibleTo()->getTimestamp() >= $time->getTimestamp());
    }

    /**
     * @return bool
     */
    public function isVisibleInCmsNav()
    {
        return $this->{self::ATTR_VISIBLE_IN_NAV_CMS};
    }

    /**
     * @return bool
     */
    public function isVisibleInNav()
    {
        return $this->{self::ATTR_VISIBLE_IN_NAV};
    }

    public function markUpdatesAsPendingApproval()
    {
        $this->addVersion(['pending_approval' => true]);

        return $this;
    }

    /**
     * @param int $groupId
     *
     * @return $this
     */
    public function removeAclGroupId($groupId)
    {
        DB::table('page_acl')
            ->where([
                'page_id'  => $this->getId(),
                'group_id' => $groupId,
            ])
            ->delete();

        return $this;
    }

    public function removeRelation(PageInterface $page)
    {
        $this->relations()->detach($page);

        return $this;
    }

    public function removeTag(TagInterface $tag)
    {
        $this->tags()->detach($tag);

        return $this;
    }

    public function parent()
    {
        return $this->belongsTo(self::class, self::ATTR_PARENT, 'id');
    }

    public function relations()
    {
        return $this->belongsToMany(self::class, 'pages_relations', 'page_id', 'related_page_id');
    }

    /**
     * @param bool $enabled
     *
     * @return $this
     */
    public function setAclEnabled($enabled)
    {
        $this->{self::ATTR_ENABLE_ACL} = $enabled;

        return $this;
    }

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setAddPageBehaviour($value)
    {
        $this->{self::ATTR_ADD_BEHAVIOUR} = $value;

        return $this;
    }

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setChildAddPageBehaviour($value)
    {
        $this->{self::ATTR_CHILD_ADD_BEHAVIOUR} = $value;

        return $this;
    }

    /**
     * Set the current version of the page.
     *
     * @param PageVersion $version
     *
     * @return $this
     */
    public function setCurrentVersion(PageVersionInterface $version)
    {
        $this->currentVersion = $version;

        return $this;
    }

    public function setDisableDelete($value)
    {
        $this->{self::ATTR_DISABLE_DELETE} = $value;

        return $this;
    }

    public function setChildTemplateId($templateId)
    {
        $this->{self::ATTR_CHILD_TEMPLATE} = $templateId;

        return $this;
    }

    /**
     * @param string $column
     * @param string $direction
     */
    public function setChildOrderingPolicy($column, $direction)
    {
        $column = constant(self::class.'::ORDER_'.strtoupper($column));
        $direction = ($direction === 'asc') ? self::ORDER_ASC : self::ORDER_DESC;

        $this->{self::ATTR_CHILD_ORDERING_POLICY} = $column | $direction;

        return $this;
    }

    /**
     * @param string $prefix
     *
     * @return $this
     */
    public function setChildrenUrlPrefix($prefix)
    {
        $this->{self::ATTR_CHILD_URL_PREFIX} = $prefix;

        return $this;
    }

    /**
     * @param bool $visible
     *
     * @return $this
     */
    public function setChildrenVisibleInNav($visible)
    {
        $this->{self::ATTR_CHILDREN_VISIBLE_IN_NAV} = $visible;

        return $this;
    }

    /**
     * @param bool $visible
     *
     * @return $this
     */
    public function setChildrenVisibleInNavCMS($visible)
    {
        $this->{self::ATTR_CHILDREN_VISIBLE_IN_NAV_CMS} = $visible;

        return $this;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->{self::ATTR_DESCRIPTION} = $description;

        return $this;
    }

    /**
     * @param bool $indexing
     *
     * @return $this
     */
    public function setExternalIndexing($indexing)
    {
        $this->{self::ATTR_EXTERNAL_INDEXING} = $indexing;

        return $this;
    }

    /**
     * @param int $templateId
     *
     * @return $this
     */
    public function setGrandchildTemplateId($templateId)
    {
        $this->{self::ATTR_GRANDCHILD_TEMPLATE} = $templateId;

        return $this;
    }

    /**
     * Set an embargo time for any unpublished changes.
     *
     * If the time is in the past then the changes become published
     *
     * @param DateTime $time
     *
     * @return $this
     */
    public function setEmbargoTime(DateTime $time)
    {
        $this->addVersion([
            PageVersion::ATTR_PENDING_APPROVAL => false,
            PageVersion::ATTR_EMBARGOED_UNTIL  => $time->getTimestamp(),
        ]);

        return $this;
    }

    /**
     * @param bool $indexing
     *
     * @return $this
     */
    public function setInternalIndexing($indexing)
    {
        $this->{self::ATTR_INTERNAL_INDEXING} = $indexing;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setInternalName($name)
    {
        $this->{self::ATTR_INTERNAL_NAME} = $name;

        return $this;
    }

    /**
     * @param string $keywords
     *
     * @return $this
     */
    public function setKeywords($keywords)
    {
        $this->{self::ATTR_KEYWORDS} = $keywords;

        return $this;
    }

    /**
     * @param PageInterface $parent
     *
     * @return $this
     */
    public function setParent(PageInterface $parent)
    {
        if (!$parent->is($this) && $parent->getParentId() !== $this->getId()) {
            $this->{self::ATTR_PARENT} = $parent->getId();
        }

        return $this;
    }

    public function setPrimaryUri($uri)
    {
        $this->{self::ATTR_PRIMARY_URI} = $uri;

        return $this;
    }

    /**
     * @param int $sequence
     *
     * @return $this
     */
    public function setSequence($sequence)
    {
        $this->{self::ATTR_SEQUENCE} = $sequence;

        return $this;
    }

    /**
     * @param TemplateInterface $template
     *
     * @return $this
     */
    public function setTemplate(TemplateInterface $template)
    {
        $this->addVersion(['template_id' => $template->getId()]);

        return $this;
    }

    public function setTitle($title)
    {
        $this->addVersion(['title' => $title]);

        return $this;
    }

    /**
     * @param bool $visible
     *
     * @return $this
     */
    public function setVisibleAtAnyTime($visible)
    {
        $this->attributes[self::ATTR_VISIBLE] = $visible;

        return $this;
    }

    /**
     * @param DateTime $time
     *
     * @return $this
     */
    public function setVisibleFrom(DateTime $time = null)
    {
        $this->{self::ATTR_VISIBLE_FROM} = ($time === null ? null : $time->getTimestamp());

        return $this;
    }

    /**
     * @param bool $visible
     *
     * @return $this
     */
    public function setVisibleInCmsNav($visible)
    {
        $this->{self::ATTR_VISIBLE_IN_NAV_CMS} = $visible;

        return $this;
    }

    /**
     * @param bool $visible
     *
     * @return $this
     */
    public function setVisibleInNav($visible)
    {
        $this->{self::ATTR_VISIBLE_IN_NAV} = $visible;

        return $this;
    }

    /**
     * @param DateTime $time
     *
     * @return $this
     */
    public function setVisibleTo(DateTime $time = null)
    {
        $this->{self::ATTR_VISIBLE_TO} = $time ? $time->getTimestamp() : null;

        return $this;
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'pages_tags', 'page_id', 'tag_id');
    }

    /**
     * Returns the URL object for the page's primary URI.
     *
     * The URL can be displayed by casting the returned object to a string:
     *
     *        (string) $page->url();
     *
     * @return URLInterface|null
     */
    public function url()
    {
        if ($this->{self::ATTR_PRIMARY_URI} === null) {
            return;
        }

        if ($this->primaryUrl === null) {
            $this->primaryUrl = new URL([
                'page'       => $this,
                'location'   => $this->{self::ATTR_PRIMARY_URI},
                'is_primary' => true,
            ]);
        }

        return $this->primaryUrl;
    }

    public function getCurrentVersionQuery()
    {
        $query = DB::table('page_versions')
            ->select([DB::raw('max(id) as id'), 'page_id'])
            ->groupBy('page_id');

        if (Editor::isDisabled()) {
            $query->where('embargoed_until', '<=', Editor::getTime()->getTimestamp());
        }

        if (Editor::isHistory()) {
            $query->where('created_at', '<=', Editor::getTime()->getTimestamp());
        }

        return $query;
    }

    /**
     * @param Builder $query
     * @param string  $title
     * @param int     $limit
     *
     * @return Builder
     */
    public function scopeAutocompleteTitle(Builder $query, $title, $limit)
    {
        return $query
            ->currentVersion()
            ->select('title', 'primary_uri')
            ->where('title', 'like', '%'.$title.'%')
            ->limit($limit)
            ->orderBy(DB::raw('length(title)'), 'asc');
    }

    /**
     * Scope for getting pages with the current version.
     *
     * This doesn't work as a global scope as changing the select columns breaks queries which add select columns.
     *
     * e.g. finding pages by related tags.
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeCurrentVersion(Builder $query)
    {
        $subquery = $this->getCurrentVersionQuery();

        return $query
            ->select('version.*')
            ->addSelect('version.id as version:id')
            ->addSelect('version.created_at as version:created_at')
            ->addSelect('version.created_by as version:created_by')
            ->addSelect('pages.*')
            ->join(DB::raw('('.$subquery->toSql().') as v2'), 'pages.id', '=', 'v2.page_id')
            ->mergeBindings($subquery)
            ->join('page_versions as version', function (JoinClause $join) {
                $join
                    ->on('pages.id', '=', 'version.page_id')
                    ->on('v2.id', '=', 'version.id');
            });
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeIsVisible(Builder $query)
    {
        return $query->isVisibleAtTime(time());
    }

    /**
     * @param Builder $query
     * @param int     $time
     *
     * @return Builder
     */
    public function scopeIsVisibleAtTime(Builder $query, $time)
    {
        return $query
            ->where('visible', '=', true)
            ->where('visible_from', '<=', $time)
            ->where(function (Builder $query) use ($time) {
                $query
                    ->where('visible_to', '>=', $time)
                    ->orWhere('visible_to', '=', 0);
            });
    }

    /**
     * @param string $value
     */
    public function setDescriptionAttribute($value)
    {
        $this->attributes[self::ATTR_DESCRIPTION] = strip_tags($value);
    }

    public function setInternalNameAttribute($value)
    {
        $value = strtolower(preg_replace('|[^-_0-9a-zA-Z]|', '', $value));

        $this->attributes[self::ATTR_INTERNAL_NAME] = $value ? $value : null;
    }

    /**
     * @param string $value
     */
    public function setPrimaryUriAttribute($value)
    {
        $this->attributes[self::ATTR_PRIMARY_URI] = URLHelper::sanitise($value);
    }
}
