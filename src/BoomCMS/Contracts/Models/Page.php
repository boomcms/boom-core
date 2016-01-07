<?php

namespace BoomCMS\Contracts\Models;

use DateTime;

interface Page
{
    /**
     * Add a related page.
     *
     * @param Page $page
     *
     * @return $this
     */
    public function addRelation(Page $page);

    /**
     * Add a tag to the page.
     *
     * @param Tag $tag
     *
     * @return $this
     */
    public function addTag(Tag $tag);

    /**
     * @return bool
     */
    public function allowsExternalIndexing();

    /**
     * @return bool
     */
    public function allowsInternalIndexing();

    /**
     * @return bool
     */
    public function canBeDeleted();

    /**
     * @return bool
     */
    public function childrenAreVisibleInNav();

    /**
     * @return bool
     */
    public function childrenAreVisibleInCmsNav();

    /**
     * @return bool
     */
    public function childShouldPromptOnAddPage();

    /**
     * @return int
     */
    public function countChildren();

    /**
     * @return $this
     */
    public function deleteDrafts();

    /**
     * @return int
     */
    public function getAddPageBehaviour();

    /**
     * Returns the Page to use as the parent when the add page button is used on this page.
     *
     * Either the current page or its parent depending on the add page behaviour settings.
     *
     * @return Page
     */
    public function getAddPageParent();

    /**
     * @return int
     */
    public function getChildAddPageBehaviour();

    /**
     * Returns an array of [column, direction].
     *
     * @return array
     */
    public function getChildOrderingPolicy();

    /**
     * @return string
     */
    public function getChildPageUrlPrefix();

    /**
     * @return int
     */
    public function getCreatedBy();

    /**
     * @return DateTime
     */
    public function getCreatedTime();

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @return int
     */
    public function getDefaultChildTemplateId();

    /**
     * @return Asset
     */
    public function getFeatureImage();

    /**
     * @return int
     */
    public function getFeatureImageId();

    /**
     * @return int
     */
    public function getGrandchildTemplateId();

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getInternalName();

    /**
     * @return string
     */
    public function getKeywords();

    /**
     * @return DateTime
     */
    public function getLastModified();

    /**
     * @return DateTime
     */
    public function getLastPublishedTime();

    /**
     * @return int
     */
    public function getManualOrderPosition();

    /**
     * @return Page
     */
    public function getParent();

    /**
     * @return int
     */
    public function getParentId();

    /**
     * @return Template
     */
    public function getTemplate();

    /**
     * @return int
     */
    public function getTemplateId();

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return array
     */
    public function getUrls();

    /**
     * @return DateTime
     */
    public function getVisibleFrom();

    /**
     * @return DateTime
     */
    public function getVisibleTo();

    /**
     * @return bool
     */
    public function hasChildren();

    /**
     * @return bool
     */
    public function hasFeatureImage();

    /**
     * @return bool
     */
    public function isDeleted();

    /**
     * Returns whether this page is the parent of a given page.
     *
     * @param Page $page
     *
     * @return bool
     */
    public function isParentOf(Page $page);

    /**
     * Returns true if the page doesn't have a parent.
     *
     * @return bool
     */
    public function isRoot();

    /**
     * Returns true if the page is visible at the current time.
     *
     * @return bool
     */
    public function isVisible();

    /**
     * @return bool
     */
    public function isVisibleAtAnyTime();

    /**
     * @param DateTime
     *
     * @return bool
     */
    public function isVisibleAtTime(DateTime $time);

    /**
     * @return bool
     */
    public function isVisibleInCmsNav();

    /**
     * @return bool
     */
    public function isVisibleInNav();

    /**
     * @return $this
     */
    public function markUpdatesAsPendingApproval();

    /**
     * Remove the relationship with another page.
     *
     * @param Page $page
     *
     * @return $this
     */
    public function removeRelation(Page $page);

    /**
     * Remove a tag from the page.
     *
     * @param Tag $tag
     *
     * @return $this
     */
    public function removeTag(Tag $tag);

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setAddPageBehaviour($value);

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setChildAddPageBehaviour($value);

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setDisableDelete($value);

    /**
     * @param int
     *
     * @return $this
     */
    public function setChildTemplateId($id);

    /**
     * @param string $column
     * @param string $direction
     *
     * @return $this
     */
    public function setChildOrderingPolicy($column, $direction);

    /**
     * @param string $prefix
     *
     * @return $this
     */
    public function setChildrenUrlPrefix($prefix);

    /**
     * @param bool $visible
     *
     * @return $this
     */
    public function setChildrenVisibleInNav($visible);

    /**
     * @param bool $visible
     *
     * @return $this
     */
    public function setChildrenVisibleInNavCMS($visible);

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description);

    /**
     * @param bool $indexing
     *
     * @return $this
     */
    public function setExternalIndexing($indexing);

    /**
     * @param int $featureImageId
     *
     * @return $this
     */
    public function setFeatureImageId($featureImageId);

    /**
     * @param int $templateId
     *
     * @return $this
     */
    public function setGrandchildTemplateId($templateId);

    /**
     * Set an embargo time for the current version.
     *
     * @param DateTime $time
     *
     * @return $this
     */
    public function setEmbargoTime(DateTime $time);

    /**
     * @param bool $indexing
     *
     * @return $this
     */
    public function setInternalIndexing($indexing);

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setInternalName($name);

    /**
     * @param string $keywords
     *
     * @return $this
     */
    public function setKeywords($keywords);

    /**
     * @param Page $parent
     *
     * @return $this
     */
    public function setParent(self $parent);

    /**
     * Set the primary URI attribute.
     *
     * @param string
     *
     * @return $this
     */
    public function setPrimaryUri($uri);

    /**
     * @param int $sequence
     *
     * @return $this
     */
    public function setSequence($sequence);

    /**
     * @param Template $template
     *
     * @return $this
     */
    public function setTemplate(Template $template);

    /**
     * Set the title of the page.
     *
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title);

    /**
     * @param bool $visible
     *
     * @return $this
     */
    public function setVisibleAtAnyTime($visible);

    /**
     * @param DateTime $time
     *
     * @return $this
     */
    public function setVisibleFrom(DateTime $time);

    /**
     * @param bool $visible
     *
     * @return $this
     */
    public function setVisibleInCmsNav($visible);

    /**
     * @param bool $visible
     *
     * @return $this
     */
    public function setVisibleInNav($visible);

    /**
     * @param DateTime $time
     *
     * @return $this
     */
    public function setVisibleTo(DateTime $time = null);

    /**
     * Whether or not the add page button should show a prompt
     *
     * @return bool
     */
    public function shouldPromptOnAddPage();

    /**
     * @return URL
     */
    public function url();

    /**
     * Returns true if the given person created the page.
     *
     * @param Person $person
     *
     * @return bool
     */
    public function wasCreatedBy(Person $person);
}
