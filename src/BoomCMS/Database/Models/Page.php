<?php

namespace BoomCMS\Database\Models;

use BoomCMS\Contracts\Models\Page as PageInterface;
use BoomCMS\Contracts\Models\PageVersion as PageVersionInterface;
use BoomCMS\Contracts\Models\Person as PersonInterface;
use BoomCMS\Contracts\Models\Tag as TagInterface;
use BoomCMS\Contracts\Models\Template as TemplateInterface;
use BoomCMS\Contracts\Models\URL as URLInterface;
use BoomCMS\Support\Facades\Editor;
use BoomCMS\Support\Helpers\URL as URLHelper;
use BoomCMS\Support\Traits\Comparable;
use BoomCMS\Support\Traits\SingleSite;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Page extends Model implements PageInterface
{
    use Comparable;
    use SingleSite;
    use SoftDeletes;

    const ATTR_ID = 'id';
    const ATTR_SEQUENCE = 'sequence';
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
    const ATTR_SITE = 'site_id';

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

    protected $casts = [
        self::ATTR_ADD_BEHAVIOUR       => 'integer',
        self::ATTR_CHILD_ADD_BEHAVIOUR => 'integer',
        self::ATTR_CHILD_TEMPLATE      => 'integer',
        self::ATTR_DESCRIPTION         => 'string',
        self::ATTR_GRANDCHILD_TEMPLATE => 'integer',
    ];

    /**
     * @var PageVersion
     */
    protected $currentVersion;

    protected $table = 'pages';

    public $guarded = [
        self::ATTR_ID,
    ];

    public $timestamps = false;

    /**
     * @var Asset
     */
    protected $featureImage;

    /**
     * @var URLInterface
     */
    protected $primaryUrl;

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

        $newVersion = new PageVersion($attrs);
        $newVersion
            ->setPage($this)
            ->setEditedAt(new DateTime('now'))
            ->setEditedBy(Auth::user());

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

    public function allowsExternalIndexing()
    {
        return (bool) $this->{self::ATTR_EXTERNAL_INDEXING} === true;
    }

    public function allowsInternalIndexing()
    {
        return (bool) $this->{self::ATTR_INTERNAL_INDEXING} === true;
    }

    public function canBeDeleted()
    {
        return (bool) $this->{self::ATTR_DISABLE_DELETE} === false;
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function childrenAreVisibleInNav()
    {
        return (bool) $this->{self::ATTR_CHILDREN_VISIBLE_IN_NAV} === true;
    }

    public function childrenAreVisibleInCmsNav()
    {
        return (bool) $this->{self::ATTR_CHILDREN_VISIBLE_IN_NAV_CMS} === true;
    }

    /**
     * @return int
     */
    public function countChildren()
    {
        return $this->children->count();
    }

    public function createdBy()
    {
        return $this->hasOne(Person::class, Person::ATTR_ID, self::ATTR_CREATED_BY);
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

    public function getChildOrderingPolicy()
    {
        $order = $this->{self::ATTR_CHILD_ORDERING_POLICY};

        if ($order & static::ORDER_TITLE) {
            $column = 'title';
        } elseif ($order & static::ORDER_VISIBLE_FROM) {
            $column = 'visible_from';
        } else {
            $column = 'sequence';
        }

        $direction = ($order & static::ORDER_ASC) ? 'asc' : 'desc';

        return [$column, $direction];
    }

    public function getChildPageUrlPrefix()
    {
        return $this->{self::ATTR_CHILD_URL_PREFIX};
    }

    /**
     * @return PersonInterface
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
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

    public function getDefaultChildTemplateId()
    {
        if ($templateId = $this->{self::ATTR_CHILD_TEMPLATE}) {
            return $templateId;
        }

        $parent = $this->getParent();

        return ($parent && $parent->getGrandchildTemplateId() != 0) ? $parent->getGrandchildTemplateId() : $this->getTemplateId();
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

        return $grandchildTemplateId !== 0 ?: $this->getTemplateId();
    }

    /**
     * @return AssetInterface
     */
    public function getFeatureImage()
    {
        if ($this->featureImage === null) {
            $this->featureImage = $this->belongsTo(Asset::class, self::ATTR_FEATURE_IMAGE)->first();
        }

        return $this->featureImage;
    }

    public function getFeatureImageId()
    {
        return $this->{self::ATTR_FEATURE_IMAGE};
    }

    public function getGrandchildTemplateId()
    {
        return $this->{self::ATTR_GRANDCHILD_TEMPLATE};
    }

    /**
     * @return int
     */
    public function getId()
    {
        return  (int) $this->{self::ATTR_ID};
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

    public function getLastPublishedTime()
    {
        $m = PageVersion::forPage($this)
            ->lastPublished()
            ->first();

        return (new DateTime())->setTimestamp($m['embargoed_until']);
    }

    public function getManualOrderPosition()
    {
        return $this->{self::ATTR_SEQUENCE};
    }

    /**
     * @return PageInterface
     */
    public function getParent()
    {
        return $this->parent;
    }

    public function getParentId()
    {
        return (int) $this->{self::ATTR_PARENT};
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

    public function getTitle()
    {
        return $this->getCurrentVersion()->getTitle();
    }

    public function getUrls()
    {
        return $this->hasMany(URL::class)->orderBy(URL::ATTR_LOCATION, 'asc')->get();
    }

    /**
     * @return null|DateTime
     */
    public function getVisibleFrom()
    {
        if ($this->{self::ATTR_VISIBLE_FROM} < 1) {
            return;
        }

        return new DateTime('@'.$this->{self::ATTR_VISIBLE_FROM});
    }

    /**
     * @return DateTime
     */
    public function getVisibleTo()
    {
        return $this->{self::ATTR_VISIBLE_TO} == 0 ? null : new DateTime('@'.$this->{self::ATTR_VISIBLE_TO});
    }

    /**
     * @return bool
     */
    public function hasChildren()
    {
        return $this->countChildren() > 0;
    }

    public function hasFeatureImage()
    {
        return $this->getFeatureImageId() != 0;
    }

    public function isDeleted()
    {
        return $this->{self::ATTR_DELETED_AT} != null;
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
        return $this->getParentId() == null;
    }

    public function isVisible()
    {
        return $this->isVisibleAtTime(new DateTime('now'));
    }

    /**
     * @param DateTime $time
     *
     * @return bool
     */
    public function isVisibleAtTime(DateTime $time)
    {
        return $this->{self::ATTR_VISIBLE_FROM} > 0 &&
            $this->{self::ATTR_VISIBLE_FROM} <= $time->getTimestamp() &&
            ($this->getVisibleTo() === null || $this->getVisibleTo()->getTimestamp() >= $time->getTimestamp());
    }

    public function isVisibleInCmsNav()
    {
        return $this->{self::ATTR_VISIBLE_IN_NAV_CMS} == true;
    }

    public function isVisibleInNav()
    {
        return $this->{self::ATTR_VISIBLE_IN_NAV} == true;
    }

    public function markUpdatesAsPendingApproval()
    {
        $this->addVersion(['pending_approval' => true]);

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

    public function setChildTemplateId($id)
    {
        $this->{self::ATTR_CHILD_TEMPLATE} = $id;

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
     * @param int $featureImageId
     *
     * @return $this
     */
    public function setFeatureImageId($featureImageId)
    {
        $this->{self::ATTR_FEATURE_IMAGE} = $featureImageId > 0 ? $featureImageId : null;

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
     * @param DateTime $time
     *
     * @return $this
     */
    public function setVisibleFrom(DateTime $time = null)
    {
        $this->{self::ATTR_VISIBLE_FROM} = $time ? $time->getTimestamp() : null;

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
     *		(string) $page->url();
     *
     *
     * @return URLInterface|null
     */
    public function url($refresh = false)
    {
        if ($this->{self::ATTR_PRIMARY_URI} === null) {
            return;
        }

        if ($refresh || $this->primaryUrl === null) {
            $this->primaryUrl = new URL([
                'page'       => $this,
                'location'   => $this->{self::ATTR_PRIMARY_URI},
                'is_primary' => true,
            ]);
        }

        return $this->primaryUrl;
    }

    public function wasCreatedBy(PersonInterface $person)
    {
        return $this->getCreatedBy() === $person->getId();
    }

    public function getCurrentVersionQuery()
    {
        $query = DB::table('page_versions')
            ->select([DB::raw('max(id) as id'), 'page_id'])
            ->groupBy('page_id');

        if (Editor::isDisabled()) {
            $query->where('embargoed_until', '<=', time());
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
            ->addSelect('pages.*')
            ->join(DB::raw('('.$subquery->toSql().') as v2'), 'pages.id', '=', 'v2.page_id')
            ->mergeBindings($subquery)
            ->join('page_versions as version', function ($join) {
                $join
                    ->on('pages.id', '=', 'version.page_id')
                    ->on('v2.id', '=', 'version.id');
            });
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
            ->whereBetween('visible_from', [1, $time])
            ->where(function ($query) use ($time) {
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
