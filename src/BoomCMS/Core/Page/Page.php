<?php

namespace BoomCMS\Core\Page;

use BoomCMS\Core\Person;
use BoomCMS\Core\Tag;
use BoomCMS\Core\Template;
use BoomCMS\Core\URL\URL;
use BoomCMS\Core\Models\Page\Version as VersionModel;

use \DateTime;

class Page
{
    /**
     *
     * @var Page\Version
     */
    private $currentVersion;

    /**
	 *
	 * @var array
	 */
    protected $data;

    /**
     *
     * @var Page
     */
    protected $parent;

    /**
	 *
	 * @var URL
	 */
    protected $primaryUrl;

    /**
     *
     * @var Template\Template
     */
    private $template;

    protected $versionColumns = [
        'version:id',
        'page_id',
        'template_id',
        'title',
        'edited_by',
        'edited_time',
        'published',
        'embargoed_until',
        'stashed',
        'pending_approval',
    ];

    public function __construct(array $data)
    {
        $versionData = [];

        foreach (array_keys($data) as $key) {
            if (in_array($key, $this->versionColumns)) {
                $versionData[$key] = $data[$key];
                unset($data[$key]);
            }
        }

        if ( !empty($versionData)) {
            $this->currentVersion = new Version($versionData);
        }

        $this->data = $data;
    }

    public function addTag(Tag\Tag $tag)
    {
        DB::insert('pages_tags', ['page_id', 'tag_id'])
            ->values([$this->getId(), $tag->getId()])
            ->execute();

        return $this;
    }

    public function allowsExternalIndexing()
    {
        return $this->get('external_indexing') == true;
    }

    public function allowsInternalIndexing()
    {
        return $this->get('internal_indexing') == true;
    }

    public function childrenAreVisibleInNav()
    {
        return $this->get('children_visible_in_nav') == true;
    }

    public function childrenAreVisibleInCmsNav()
    {
        return $this->get('children_visible_in_nav_cms') == true;
    }

    public function addVersion(array $attrs = [])
    {
        if ($currentVersion = $this->getCurrentVersion()) {
            $attrs = array_merge($currentVersion->toArray(), $attrs, ['page_id' => $this->getId()]);
        }

        $this->currentVersion = new Version(VersionModel::create($attrs)->toArray());
    }

    public function get($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    public function getChildOrderingPolicy()
    {
        return new ChildOrderingPolicy($this->get('children_ordering_policy'));
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
	 *
	 * @return DateTime
	 */
    public function getCreatedTime()
    {
        return new DateTime('@' . $this->get('created_time'));
    }

    public function getCurrentVersion()
    {
        if ($this->currentVersion === null) {
            if ($this->loaded()) {
                $version = VersionModel::where('page_id', '=', $this->getId())->latestPublished()->first();
                
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
        $description = ($this->get('description') != null) ? $this->get('description') : \Chunk::factory('text', 'standfirst', $this)->text();

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

    public function getFeatureImage()
    {
        // TODO:: return an asset instance
        return \Boom\Asset\Factory::fromModel($this->getFeatureImageId());
    }

    public function getFeatureImageId()
    {
        return $this->get('feature_image_id');
    }

    public function getGrandchildTemplateId()
    {
        return $this->get('grandchild_template_id');
    }

    public function getGroupedTags()
    {
        $tags = $this->getTags();
        $grouped = [];

        foreach ($tags as $tag) {
            $grouped[$tag->getGroup()][] = $tag;
        }

        return $grouped;
    }

    public function getId()
    {
        return $this->get('id');
    }

    public function getInternalName()
    {
        return $this->get('internal_name');
    }

    /**
	 *
	 * @return Keywords
	 */
    public function getKeywords()
    {
        return $this->get('keywords');
    }

    /**
     *
     * @return DateTime
     */
    public function getLastModified()
    {
        return new DateTime('@' . $this->getCurrentVersion()->edited_time);
    }

    public function getManualOrderPosition()
    {
        return $this->get('sequence');
    }

    public function getParent()
    {
        if ($this->getParentId()) {
            if ($this->parent === null) {
                $provider = new Provider();
                $this->parent = $provider->findById($this->getParentId());

                return $this->parent;
            }
        }
    }

    public function getParentId()
    {
        return $this->get('parent_id');
    }

    public function getTemplate()
    {
        return $this->getCurrentVersion()->getTemplate();
    }

    public function getTemplateId()
    {
        return $this->getCurrentVersion()->template_id;
    }

    public function getTitle()
    {
        return $this->getCurrentVersion()->getTitle();
    }

    /**
	 *
	 * @return DateTime
	 */
    public function getVisibleFrom()
    {
        $timestamp = $this->get('visible_from') ?: time();

        return new DateTime('@' . $timestamp);
    }

    /**
	 *
	 * @return DateTime
	 */
    public function getVisibleTo()
    {
        return $this->get('visible_to') == 0 ? null : new DateTime('@' . $this->get('visible_to'));
    }

    public function hasFeatureImage()
    {
        return $this->getFeatureImageId() != 0;
    }

    public function isDeleted()
    {
        return $this->get('deleted') === false;
    }

    public function isRoot()
    {
        return $this->getParentId() == null;
    }

    public function isVisible()
    {
        return $this->isVisibleAtTime(time());
    }

    public function isVisibleAtAnyTime()
    {
        return $this->get('visible') == true;
    }

    /**
	 *
	 * @param int $unixTimestamp
	 * @return boolean
	 */
    public function isVisibleAtTime($unixTimestamp)
    {
        return ($this->isVisibleAtAnyTime() &&
            $this->getVisibleFrom()->getTimestamp() <= $unixTimestamp &&
            ($this->getVisibleTo() === null || $this->getVisibleTo()->getTimestamp() >= $unixTimestamp)
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

    public function loaded()
    {
        return $this->getId() > 0;
    }

    public function removeTag(Tag\Tag $tag)
    {
        DB::delete('pages_tags')
            ->where('page_id', '=', $this->getId())
            ->where('tag_id', '=', $tag->getId())
            ->execute();

        return $this;
    }

    public function setChildTemplateId($id)
    {
        $this->data['children_template_id'] = $id;

        return $this;
    }

    /**
	 *
	 * @param	string	$column
	 * @param	string	$direction
	 */
    public function setChildOrderingPolicy($column, $direction)
    {
        $ordering_policy = new \Boom\Page\ChildOrderingPolicy($column, $direction);
        $this->data['children_ordering_policy'] = $ordering_policy->asInt();

        return $this;
    }

    /**
     *
     * @param  string          $prefix
     * @return \Boom\Page\Page
     */
    public function setChildrenUrlPrefix($prefix)
    {
        $this->data['children_url_prefix'] = $prefix;

        return $this;
    }

    /**
     *
     * @param  boolean         $visible
     * @return \Boom\Page\Page
     */
    public function setChildrenVisibleInNav($visible)
    {
        $this->data['children_visible_in_nav'] = $visible;

        return $this;
    }

    /**
     *
     * @param  boolean         $visible
     * @return \Boom\Page\Page
     */
    public function setChildrenVisibleInNavCMS($visible)
    {
        $this->data['children_visible_in_nav_cms'] = $visible;

        return $this;
    }

    /**
	 *
	 * @param string $description
	 * @return \Boom\Page\Page
	 */
    public function setDescription($description)
    {
        $this->data['description'] = $description;

        return $this;
    }

    /**
	 *
	 * @param boolean $indexing
	 * @return \Boom\Page\Page
	 */
    public function setExternalIndexing($indexing)
    {
        $this->data['external_indexing'] = $indexing;

        return $this;
    }

    /**
	 *
	 * @param int $featureImageId
	 * @return \Boom\Page\Page
	 */
    public function setFeatureImageId($featureImageId)
    {
        $this->data['feature_image_id'] = $featureImageId > 0 ? $featureImageId : null;

        return $this;
    }

    /**
     *
     * @param  int             $templateId
     * @return \Boom\Page\Page
     */
    public function setGrandchildTemplateId($templateId)
    {
        $this->data['grandchild_template_id'] = $templateId;

        return $this;
    }

    public function setId($id)
    {
        if ( !$this->getId()) {
            $this->attributes['id'] = $id;
        }

        return $this;
    }

    /**
	 *
	 * @param boolean $indexing
	 * @return \Boom\Page\Page
	 */
    public function setInternalIndexing($indexing)
    {
        $this->data['internal_indexing'] = $indexing;

        return $this;
    }

    /**
	 *
	 * @param string $name
	 * @return \Boom\Page\Page
	 */
    public function setInternalName($name)
    {
        $this->data['internal_name'] = $name;

        return $this;
    }

    /**
	 *
	 * @param string $keywords
	 * @return \Boom\Page\Page
	 */
    public function setKeywords($keywords)
    {
        $this->data['keywords'] = $keywords;

        return $this;
    }

    /**
	 *
	 * @param int $parentId
	 * @return \Boom\Page\Page
	 */
    public function setParentPageId($parentId)
    {
        $this->data['parent_id'] = $parentId;

        return $this;
    }

    public function setPrimaryUri($uri)
    {
        $this->data['primary_uri'] = $uri;

        return $this;
    }

    /**
	 *
	 * @param boolean $visible
	 * @return \Boom\Page\Page
	 */
    public function setVisibleAtAnyTime($visible)
    {
        $this->data['visible'] = $visible;

        return $this;
    }

    /**
	 *
	 * @param DateTime $time
	 * @return \Boom\Page\Page
	 */
    public function setVisibleFrom(DateTime $time)
    {
        $this->data['visible_from'] = $time->getTimestamp();

        return $this;
    }

    /**
	 *
	 * @param boolean $visible
	 * @return \Boom\Page\Page
	 */
    public function setVisibleInCmsNav($visible)
    {
        $this->data['visible_in_nav_cms'] = $visible;

        return $this;
    }

    /**
	 *
	 * @param boolean $visible
	 * @return \Boom\Page\Page
	 */
    public function setVisibleInNav($visible)
    {
        $this->data['visible_in_nav'] = $visible;

        return $this;
    }

    /**
	 *
	 * @param DateTime $time
	 * @return \Boom\Page\Page
	 */
    public function setVisibleTo(DateTime $time = null)
    {
        $this->data['visible_to'] = $time ? $time->getTimestamp() : null;

        return $this;
    }

    public function toArray()
    {
        return $this->data;
    }

    public function updateChildSequences(array $sequences)
    {
        foreach ($sequences as $sequence => $pageId) {
            $mptt = new \Model_Page_Mptt($pageId);

            // Only update the sequence of pages which are children of this page.
            if ($mptt->scope == $this->model->mptt->scope && $mptt->parent_id == $this->getId()) {
                \DB::update('pages')
                    ->set(['sequence' => $sequence])
                    ->where('id', '=', $pageId)
                    ->execute();
            }
        }

        return $this;
    }

    /**
	 * Returns the Model_Page_URL object for the page's primary URI
	 *
	 * The URL can be displayed by casting the returned object to a string:
	 *
	 *		(string) $page->url();
	 *
	 *
	 * @return \Model_Page_URL
	 */
    public function url()
    {
        if ($this->primaryUrl === null) {
            $this->primaryUrl = new URL([
                'page' => $this,
                'location' => $this->get('primary_uri'),
                'is_primary' => true
            ]);
        }

        return $this->primaryUrl;
    }

    public function wasCreatedBy(Person\Person $person)
    {
        return $this->getCreatedBy() === $person->getId();
    }
}
