<?php

namespace BoomCMS\Contracts\Models;

interface Site
{
    /**
     * Returns the email address of the site admin.
     *
     * @return string
     */
    public function getAdminEmail();

    /**
     * Returns the analytivcs code for the site.
     *
     * @return string
     */
    public function getAnalytics();

    /**
     * Reutrns the site hostname.
     *
     * @return string
     */
    public function getHostname();

    /**
     * Returns the ID of the site.
     *
     * @return int
     */
    public function getId();

    /**
     * Returns the name of the site.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns whether this site is the default
     *
     * @return bool
     */
    public function isDefault();

    /**
     * Sets the email of the site admin.
     *
     * @param type $email
     *
     * @return $this
     */
    public function setAdminEmail($email);

    /**
     * Set the site's analytics code.
     *
     * @param string $code
     *
     * @return $this
     */
    public function setAnalytics($code);

    /**
     * @param bool $default
     *
     * @return $this
     */
    public function setDefault($default);

    /**
     * Set the site's hostname.
     *
     * @param string $hostname
     *
     * @return $this
     */
    public function setHostname($hostname);

    /**
     * Set the name of the site.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name);
}
