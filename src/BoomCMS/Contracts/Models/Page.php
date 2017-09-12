<?php

namespace BoomCMS\Contracts\Models;

use DateTime;
use Illuminate\Support\Collection;

interface Page
{
    /**
     * Whether ACL is enabled.
     */
    public function aclEnabled(): bool;

    /**
     * Add a group which can view this page.
     */
    public function addAclGroupId(int $groupId): Page;

    /**
     * Add a related page.
     *
     * @param Page $page
     */
    public function addRelation(Page $page): Page;

    /**
     * Add a tag to the page.
     *
     * @param Tag $tag
     */
    public function addTag(Tag $tag): Page;

    public function allowsExternalIndexing(): bool;

    public function allowsInternalIndexing(): bool;

    public function canBeDeleted(): bool;

    public function childrenAreVisibleInNav(): bool;

    public function childrenAreVisibleInCmsNav(): bool;

    public function countChildren(): int;

    public function getAclGroupIds(): Collection;

    public function getAddPageBehaviour(): int;

    /**
     * Returns the Page to use as the parent when the add page button is used on this page.
     *
     * Either the current page or its parent depending on the add page behaviour settings.
     */
    public function getAddPageParent(): Page;

    public function getChildAddPageBehaviour(): int;

    /**
     * Returns an array of [column, direction].
     */
    public function getChildOrderingPolicy(): array;

    public function getChildPageUrlPrefix(): string;

    public function getCreatedBy();

    public function getCreatedTime(): DateTime;

    public function getDescription(): string;

    public function getDefaultChildTemplateId(): int;

    public function getDefaultGrandchildTemplateId(): int;

    /**
     * @return Asset|null
     */
    public function getFeatureImage();

    public function getGrandchildTemplateId(): int;

    public function getId(): int;

    public function getInternalName(): string;

    public function getKeywords(): string;

    public function getLastModified(): DateTime;

    public function getLastPublished(): PageVersion;

    public function getManualOrderPosition(): int;

    /**
     * @return Page|null
     */
    public function getParent();

    /**
     * @return int|null
     */
    public function getParentId();

    /**
     * Returns the site that the page belongs to.
     *
     * @return Site|null
     */
    public function getSite();

    /**
     * @return Template|null
     */
    public function getTemplate();

    /**
     * @return int|null
     */
    public function getTemplateId();

    /**
     * @return array
     */
    public function getUrls();

    public function getVisibleFrom(): DateTime;

    /**
     * @return DateTime|null
     */
    public function getVisibleTo();

    public function hasChildren(): bool;

    public function isDeleted(): bool;

    /**
     * Returns whether this page is the parent of a given page.
     *
     * @param Page $page
     */
    public function isParentOf(Page $page): bool;

    /**
     * Returns true if the page doesn't have a parent.
     */
    public function isRoot(): bool;

    /**
     * Returns true if the page is visible at the current time.
     */
    public function isVisible(): bool;

    public function isVisibleAtAnyTime(): bool;

    public function isVisibleAtTime(DateTime $time): bool;

    public function isVisibleInCmsNav(): bool;

    public function isVisibleInNav(): bool;

    public function markUpdatesAsPendingApproval(): Page;

    /**
     * Remove a group from being able to view this page.
     */
    public function removeAclGroupId(int $groupId): Page;

    /**
     * Remove the relationship with another page.
     */
    public function removeRelation(Page $page): Page;

    /**
     * Remove a tag from the page.
     */
    public function removeTag(Tag $tag): Page;

    /**
     * Set whether ACL is enabled for the page.
     */
    public function setAclEnabled(bool $enabled): Page;

    public function setAddPageBehaviour(int $value): Page;

    public function setChildAddPageBehaviour(int $value): Page;

    public function setCurrentVersion(PageVersion $version): Page;

    public function setDisableDelete(bool $value): Page;

    public function setChildTemplateId(int $id): Page;

    public function setChildOrderingPolicy(string $column, string $direction): Page;

    public function setChildrenUrlPrefix(string $prefix): Page;

    public function setChildrenVisibleInNav(bool $visible): Page;

    public function setChildrenVisibleInNavCMS(bool $visible): Page;

    public function setDescription(string $description): Page;

    public function setExternalIndexing(bool $indexing): Page;

    public function setFeatureImageId(int $featureImageId = null): Page;

    public function setGrandchildTemplateId(int $templateId): Page;

    public function setEmbargoTime(DateTime $time): Page;

    public function setInternalIndexing(bool $indexing): Page;

    public function setInternalName(string $name): Page;

    public function setKeywords(string $keywords): Page;

    public function setParent(Page $parent): Page;

    public function setPrimaryUri(string $uri): Page;

    public function setSequence(int $sequence): Page;

    public function setTemplate(Template $template): Page;

    public function setTitle(string $title): Page;

    public function setVisibleAtAnyTime(bool $visible): Page;

    public function setVisibleFrom(DateTime $time): Page;

    public function setVisibleInCmsNav(bool $visible): Page;

    public function setVisibleInNav(bool $visible): Page;

    public function setVisibleTo(DateTime $time = null): Page;

    public function wasCreatedBy(Person $person): bool;
}
