<?php

namespace BoomCMS\Contracts\Models;

use DateTime;

interface PageVersion
{
    /**
     * @return Page
     */
    public function getEditedBy();

    /**
     * @return DateTme
     */
    public function getEditedTime();

    /**
     * @return DateTime
     */
    public function getEmbargoedUntil();

    /**
     * @return int
     */
    public function getId();

    /**
     * @return int
     */
    public function getPageId();

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @return int
     */
    public function getTemplateId();

    /**
     * @return Template
     */
    public function getTemplate();

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return bool
     */
    public function isDraft();

    /**
     * @return bool
     */
    public function isEmbargoed();

    /**
     * @return bool
     */
    public function isPendingApproval();

    /**
     * @return bool
     */
    public function isPublished();

    /**
     * @return $this
     */
    public function makeDraft();

    /**
     * @param DateTime $time
     *
     * @return $this
     */
    public function setEditedAt(DateTime $time);

    /**
     * @param Person $person
     *
     * @return $this
     */
    public function setEditedBy(Person $person);

    /**
     * @param Page $page
     *
     * @return $this
     */
    public function setPage(Page $page);

    /**
     * @return string
     */
    public function status();
}
