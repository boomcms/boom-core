<?php

namespace BoomCMS\Contracts\Models;

use DateTime;

interface PageVersion
{
    /**
     * @return string
     */
    public function getChunkType();

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
    public function isContentChange();

    /**
     * @return bool
     */
    public function isDraft();

    /**
     * @param null|DateTime $time
     *
     * @return bool
     */
    public function isEmbargoed(DateTime $time = null);

    /**
     * @return bool
     */
    public function isPendingApproval();

    /**
     * @param null|DateTime $time
     *
     * @return bool
     */
    public function isPublished(DateTime $time = null);

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
