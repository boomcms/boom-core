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
    public function addAclGroupId(int $groupId): self;

    /**
     * Add a related page.
     *
     * @param Page $page
     */
    public function addRelation(self $page): self;

    /**
     * Add a tag to the page.
     *
     * @param Tag $tag
     */
    public function addTag(Tag $tag): self;

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
    public function getAddPageParent(): self;

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
    public function isParentOf(self $page): bool;

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

    public function markUpdatesAsPendingApproval(): self;

    /**
     * Remove a group from being able to view this page.
     */
    public function removeAclGroupId(int $groupId): self;

    /**
     * Remove the relationship with another page.
     */
    public function removeRelation(self $page): self;

    /**
     * Remove a tag from the page.
     */
    public function removeTag(Tag $tag): self;

    /**
     * Set whether ACL is enabled for the page.
     */
    public function setAclEnabled(bool $enabled): self;

    public function setAddPageBehaviour(int $value): self;

    public function setChildAddPageBehaviour(int $value): self;

    public function setCurrentVersion(PageVersion $version): self;

    public function setDisableDelete(bool $value): self;

    public function setChildTemplateId(int $id): self;

    public function setChildOrderingPolicy(string $column, string $direction): self;

    public function setChildrenUrlPrefix(string $prefix): self;

    public function setChildrenVisibleInNav(bool $visible): self;

    public function setChildrenVisibleInNavCMS(bool $visible): self;

    public function setDescription(string $description): self;

    public function setExternalIndexing(bool $indexing): self;

    public function setFeatureImageId(int $featureImageId = null): self;

    public function setGrandchildTemplateId(int $templateId): self;

    public function setEmbargoTime(DateTime $time): self;

    public function setInternalIndexing(bool $indexing): self;

    public function setInternalName(string $name): self;

    public function setKeywords(string $keywords): self;

    public function setParent(self $parent): self;

    public function setPrimaryUri(string $uri): self;

    public function setSequence(int $sequence): self;

    public function setTemplate(Template $template): self;

    public function setTitle(string $title): self;

    public function setVisibleAtAnyTime(bool $visible): self;

    public function setVisibleFrom(DateTime $time): self;

    public function setVisibleInCmsNav(bool $visible): self;

    public function setVisibleInNav(bool $visible): self;

    public function setVisibleTo(DateTime $time = null): self;

    public function wasCreatedBy(Person $person): bool;
}
