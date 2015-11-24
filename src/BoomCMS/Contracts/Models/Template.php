<?php

namespace BoomCMS\Contracts\Models;

use BoomCMS\Core\Template\Theme;
use Illuminate\View\View;

interface Template
{
    /**
     * @return bool
     */
    public function fileExists();

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @return string
     */
    public function getFilename();

    /**
     * @return int
     */
    public function getId();

    /**
     * @return Theme
     */
    public function getTheme();

    /**
     * @return string
     */
    public function getThemeName();

    /**
     * @return string
     */
    public function getFullFilename();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return View
     */
    public function getView();

    /**
     * @return string
     */
    public function getViewName();

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description);

    /**
     * @param string $filename
     *
     * @return $this
     */
    public function setFilename($filename);

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name);
}
