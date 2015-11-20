<?php

namespace BoomCMS\Core\Page;

use BoomCMS\Contracts\Models\Page as PageInterface;
use BoomCMS\Contracts\Models\Person;
use BoomCMS\Contracts\Models\Tag;
use BoomCMS\Contracts\Models\Template;
use BoomCMS\Contracts\Models\URL;
use BoomCMS\Database\Models\URL as URLModel;
use BoomCMS\Database\Models\Page\Version as VersionModel;
use BoomCMS\Support\Facades\Asset;
use BoomCMS\Support\Facades\Auth;
use BoomCMS\Support\Facades\Chunk;
use BoomCMS\Support\Facades\Page as PageFacade;
use BoomCMS\Support\Traits\Comparable;
use BoomCMS\Support\Traits\HasId;
use DateTime;
use Illuminate\Support\Facades\DB;

class Page implements PageInterface
{
    use Comparable;
    use HasId;

    const ORDER_SEQUENCE = 1;
    const ORDER_TITLE = 2;
    const ORDER_VISIBLE_FROM = 4;
    const ORDER_ASC = 8;
    const ORDER_DESC = 16;

    /**
     * @var Page\Version
     */
    private $currentVersion;

    /**
     * @var array
     */
    protected $attributes;

    /**
     * @var Page
     */
    protected $parent;

    /**
     * @var URL
     */
    protected $primaryUrl;

    protected $versionColumns = [
        'version:id',
        'page_id',
        'template_id',
        'title',
        'edited_by',
        'edited_time',
        'published',
        'embargoed_until',
        'pending_approval',
    ];

    public function __construct(array $attributes = [])
    {
        $versionData = [];

        foreach (array_keys($attributes) as $key) {
            if (in_array($key, $this->versionColumns)) {
                $versionData[$key] = $attributes[$key];
                unset($attributes[$key]);
            }
        }

        if (!empty($versionData)) {
            $this->currentVersion = new Version($versionData);
        }

        $this->attributes = $attributes;
    }

    public function addRelation(PageInterface $page)
    {
        if ($this->loaded() && $page->loaded()) {
            DB::table('pages_relations')
                ->insert([
                    'page_id'         => $this->getId(),
                    'related_page_id' => $page->getId(),
                    'created_at'      => time(),
                    'created_by'      => Auth::getPerson()->getId(),
                ]);
        }

        return $this;
    }

    public function addTag(Tag $tag)
    {
        if ($this->loaded()) {
            DB::table('pages_tags')
                ->insert([
                    'page_id' => $this->getId(),
                    'tag_id'  => $tag->getId(),
                ]);
        }

        return $this;
    }

    public function addVersion(array $attrs = [])
    {
        if ($currentVersion = $this->getCurrentVersion()) {
            $attrs = array_merge($currentVersion->toArray(), $attrs);
        }

        $attrs = array_merge($attrs, [
            'page_id'     => $this->getId(),
            'edited_by'   => Auth::getPerson()->getId(),
            'edited_time' => time(),
        ]);

        // If the embargo time of the new version is in the past, set the embargo time to null
        // This means that if the old version was published, the new version will be a draft.
        // If the embargo time is in the future don't change it.
        if (!isset($attrs['embargoed_until']) || $attrs['embargoed_until'] < time()) {
            $attrs['embargoed_until'] = null;
        }

        $this->currentVersion = new Version(VersionModel::create($attrs)->toArray());

        return $this->currentVersion;
    }

    public function allowsExternalIndexing()
    {
        return $this->get('external_indexing') == true;
    }

    public function allowsInternalIndexing()
    {
        return $this->get('internal_indexing') == true;
    }

    public function canBeDeleted()
    {
        return $this->get('disable_delete') == '0';
    }

    public function childrenAreVisibleInNav()
    {
        return $this->get('children_visible_in_nav') == true;
    }

    public function childrenAreVisibleInCmsNav()
    {
        return $this->get('children_visible_in_nav_cms') == true;
    }

    public function countChildren()
    {
        $finder = new Finder\Finder();
        $finder->addFilter(new Finder\ParentId($this->getId()));

        return $finder->count();
    }

    public function deleteDrafts()
    {
        DB::table('page_versions')
            ->where('page_id', '=', $this->getId())
            ->where(function ($query) {
                $query
                    ->whereNull('embargoed_until')
                    ->orWhere('embargoed_until', '>', time());
            })
            ->where('edited_time', '>', $this->getLastPublishedTime()->getTimestamp())
            ->delete();
    }

    public function get($key)
    {
        return isset($this->attributes[$key]) ? $this->attributes[$key] : null;
    }

    public function getChildOrderingPolicy()
    {
        $order = $this->get('children_ordering_policy');

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
        return $this->get('children_url_prefix');
    }

    public function getCreatedBy()
    {
        // TODO: this needs to return a Person object.
        return $this->get('created_by');
    }

    /**
     * @return DateTime
     */
    public function getCreatedTime()
    {
        $time = new DateTime();
        $time->setTimestamp($this->get('created_time'));

        return $time;
    }

    public function getCurrentVersion()
    {
        if ($this->currentVersion === null) {
            if ($this->loaded()) {
                $version = VersionModel::forPage($this)
                    ->latestAvailable()
                    ->first();

                if ($version) {
                    $this->currentVersion = new Version($version->toArray());
                } else {
                    $this->currentVersion = new Version([]);
                }
            } else {
                $this->currentVersion = new Version([]);
            }
        }

        return $this->currentVersion;
    }

    /**
     * Get a description for the page.
     *
     * If no description property is set then the standfirst is used instead.
     *
     * @return string
     */
    public function getDescription()
    {
        $description = ($this->get('description') != null) ?
            $this->get('description')
            : Chunk::get('text', 'standfirst', $this)->text();

        return \strip_tags($description);
    }

    public function getDefaultChildTemplateId()
    {
        if ($templateId = $this->get('children_template_id')) {
            return $templateId;
        }

        $parent = $this->getParent();

        return ($parent->getGrandchildTemplateId() != 0) ? $parent->getGrandchildTemplateId() : $this->getTemplateId();
    }

    /**
     * @return BoomCMS\Core\Asset\Asset
     */
    public function getFeatureImage()
    {
        return Asset::findById($this->getFeatureImageId());
    }

    public function getFeatureImageId()
    {
        return $this->get('feature_image_id');
    }

    public function getGrandchildTemplateId()
    {
        return $this->get('grandchild_template_id');
    }

    public function getInternalName()
    {
        return $this->get('internal_name');
    }

    /**
     * @return string
     */
    public function getKeywords()
    {
        return $this->get('keywords');
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
        $m = VersionModel::forPage($this)
            ->lastPublished()
            ->first();

        return (new DateTime())->setTimestamp($m['embargoed_until']);
    }

    public function getManualOrderPosition()
    {
        return $this->get('sequence');
    }

    public function getParent()
    {
        if ($this->getParentId()) {
            if ($this->parent === null) {
                $this->parent = PageFacade::findById($this->getParentId());
            }

            return $this->parent;
        } else {
            return new self([]);
        }
    }

    public function getParentId()
    {
        return $this->get('parent_id');
    }

    /**
     * 
     * @return Template
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
        return URLModel::where('page_id', $this->getId())->get();
    }

    /**
     * @return DateTime
     */
    public function getVisibleFrom()
    {
        $timestamp = $this->get('visible_from') ?: time();

        return new DateTime('@'.$timestamp);
    }

    /**
     * @return DateTime
     */
    public function getVisibleTo()
    {
        return $this->get('visible_to') == 0 ? null : new DateTime('@'.$this->get('visible_to'));
    }

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
        return $this->get('deleted') === false;
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

    public function isVisibleAtAnyTime()
    {
        return $this->get('visible') == true;
    }

    /**
     * @param DateTime $time
     *
     * @return bool
     */
    public function isVisibleAtTime(DateTime $time)
    {
        return ($this->isVisibleAtAnyTime() &&
            $this->getVisibleFrom()->getTimestamp() <= $time->getTimestamp() &&
            ($this->getVisibleTo() === null || $this->getVisibleTo()->getTimestamp() >= $time->getTimestamp())
        );
    }

    public function isVisibleInCmsNav()
    {
        return $this->get('visible_in_nav_cms') == true;
    }

    public function isVisibleInNav()
    {
        return $this->get('visible_in_nav') == true;
    }

    public function markUpdatesAsPendingApproval()
    {
        $this->addVersion(['pending_approval' => true]);

        return $this;
    }

    public function removeRelation(PageInterface $page)
    {
        if ($this->loaded() && $page->loaded()) {
            DB::table('pages_relations')
                ->where('page_id', '=', $this->getId())
                ->where('related_page_id', '=', $page->getId())
                ->delete();
        }

        return $this;
    }

    public function removeTag(Tag $tag)
    {
        if ($this->loaded()) {
            DB::table('pages_tags')
                ->where('page_id', '=', $this->getId())
                ->where('tag_id', '=', $tag->getId())
                ->delete();
        }

        return $this;
    }

    public function setDisableDelete($value)
    {
        $this->attributes['disable_delete'] = $value;

        return $this;
    }

    public function setChildTemplateId($id)
    {
        $this->attributes['children_template_id'] = $id;

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

        $this->attributes['children_ordering_policy'] = $column | $direction;

        return $this;
    }

    /**
     * @param string $prefix
     *
     * @return $this
     */
    public function setChildrenUrlPrefix($prefix)
    {
        $this->attributes['children_url_prefix'] = $prefix;

        return $this;
    }

    /**
     * @param bool $visible
     *
     * @return $this
     */
    public function setChildrenVisibleInNav($visible)
    {
        $this->attributes['children_visible_in_nav'] = $visible;

        return $this;
    }

    /**
     * @param bool $visible
     *
     * @return $this
     */
    public function setChildrenVisibleInNavCMS($visible)
    {
        $this->attributes['children_visible_in_nav_cms'] = $visible;

        return $this;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->attributes['description'] = $description;

        return $this;
    }

    /**
     * @param bool $indexing
     *
     * @return $this
     */
    public function setExternalIndexing($indexing)
    {
        $this->attributes['external_indexing'] = $indexing;

        return $this;
    }

    /**
     * @param int $featureImageId
     *
     * @return $this
     */
    public function setFeatureImageId($featureImageId)
    {
        $this->attributes['feature_image_id'] = $featureImageId > 0 ? $featureImageId : null;

        return $this;
    }

    /**
     * @param int $templateId
     *
     * @return $this
     */
    public function setGrandchildTemplateId($templateId)
    {
        $this->attributes['grandchild_template_id'] = $templateId;

        return $this;
    }

    public function setEmbargoTime(DateTime $time)
    {
        DB::table('page_versions')
            ->where('page_id', '=', $this->getId())
            ->where('embargoed_until', '>', time())
            ->update(['published' => false]);

        $this->addVersion([
            'pending_approval' => false,
            'published'        => true,
            'embargoed_until'  => $time->getTimestamp(),
        ]);
    }

    /**
     * @param bool $indexing
     *
     * @return $this
     */
    public function setInternalIndexing($indexing)
    {
        $this->attributes['internal_indexing'] = $indexing;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setInternalName($name)
    {
        $this->attributes['internal_name'] = $name;

        return $this;
    }

    /**
     * @param string $keywords
     *
     * @return $this
     */
    public function setKeywords($keywords)
    {
        $this->attributes['keywords'] = $keywords;

        return $this;
    }

    /**
     * @param int $parentId
     *
     * @return $this
     */
    public function setParentId($parentId)
    {
        if ($parentId && $parentId != $this->getId()) {
            $parent = PageFacade::findById($parentId);

            if ($parent->loaded()) {
                $this->attributes['parent_id'] = $parentId;
            }
        }

        return $this;
    }

    public function setPrimaryUri($uri)
    {
        $this->attributes['primary_uri'] = $uri;

        return $this;
    }

    /**
     * @param int $sequence
     *
     * @return $this
     */
    public function setSequence($sequence)
    {
        $this->attributes['sequence'] = $sequence;

        return $this;
    }

    /**
     * @param Template $template
     *
     * @return $this
     */
    public function setTemplate(Template $template)
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
        $this->attributes['visible'] = $visible;

        return $this;
    }

    /**
     * @param DateTime $time
     *
     * @return $this
     */
    public function setVisibleFrom(DateTime $time)
    {
        $this->attributes['visible_from'] = $time->getTimestamp();

        return $this;
    }

    /**
     * @param bool $visible
     *
     * @return $this
     */
    public function setVisibleInCmsNav($visible)
    {
        $this->attributes['visible_in_nav_cms'] = $visible;

        return $this;
    }

    /**
     * @param bool $visible
     *
     * @return $this
     */
    public function setVisibleInNav($visible)
    {
        $this->attributes['visible_in_nav'] = $visible;

        return $this;
    }

    /**
     * @param DateTime $time
     *
     * @return $this
     */
    public function setVisibleTo(DateTime $time = null)
    {
        $this->attributes['visible_to'] = $time ? $time->getTimestamp() : null;

        return $this;
    }

    public function toArray()
    {
        return $this->attributes;
    }

    /**
     * Returns the Model_Page_URL object for the page's primary URI.
     *
     * The URL can be displayed by casting the returned object to a string:
     *
     *		(string) $page->url();
     *
     *
     * @return URL
     */
    public function url($refresh = false)
    {
        if ($refresh || $this->primaryUrl === null) {
            $this->primaryUrl = new URLModel([
                'page'       => $this,
                'location'   => $this->get('primary_uri'),
                'is_primary' => true,
            ]);
        }

        return $this->primaryUrl;
    }

    public function wasCreatedBy(Person $person)
    {
        return $this->getCreatedBy() === $person->getId();
    }
}
