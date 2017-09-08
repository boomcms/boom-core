<?php

namespace BoomCMS\Theme;

use Illuminate\Support\Facades\File;

class Theme
{
    /**
     * The name of the init file.
     *
     * @see init()
     *
     * @var string
     */
    protected $initFilename = 'init.php';

    protected $name;
    protected $themesDir = 'boomcms/themes';

    public function __construct($name = null)
    {
        $this->name = $name;
    }

    public function __toString()
    {
        return $this->getName();
    }

    public function getConfigDirectory(): string
    {
        return $this->getDirectory().DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'config';
    }

    /**
     * Returns the root directory for this theme within the themes directory.
     */
    public function getDirectory(): string
    {
        return $this->getThemesDirectory().DIRECTORY_SEPARATOR.$this->getName();
    }

    /**
     * The directory containing database migrations for the theme.
     *
     * These will be published to the applications migrations/boomcms directory
     * Allowing all theme defined migrations to be applied via `php artisan migrate --path=migrations/boomcms`
     */
    public function getMigrationsDirectory(): string
    {
        return $this->getDirectory().DIRECTORY_SEPARATOR.'migrations';
    }

    /**
     * The directory containing the theme's public files.
     *
     * These will be published to the application's main public directory in order to make them accessible to the webserver
     *
     * @see https://github.com/boomcms/boom-core/blob/master/src/BoomCMS/Console/Commands/Publish.php
     */
    public function getPublicDirectory(): string
    {
        return $this->getDirectory().DIRECTORY_SEPARATOR.'public';
    }

    /**
     * The directory where BoomCMS templates are stored.
     *
     * This will be scanned for new files to install new templates
     *
     * @see https://github.com/boomcms/boom-core/blob/master/src/BoomCMS/Console/Commands/InstallTemplates.php
     */
    public function getTemplateDirectory(): string
    {
        return $this->getViewDirectory().DIRECTORY_SEPARATOR.'templates';
    }

    /**
     * Returns the path of the directory containing all the BoomCMS themes.
     *
     * This is usually the boomcms/themes directory within the application's storage directory
     */
    public function getThemesDirectory(): string
    {
        return storage_path($this->themesDir);
    }

    public function getViewDirectory(): string
    {
        return $this->getDirectory().DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'views';
    }

    /**
     * Returns the name of the theme.
     *
     * The theme's name is used to namespace views etc. to isolate themes from one another
     *
     * The name is taken from name of the directory containing the theme within the themes directory
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Includes the theme's init file, if it exists.
     *
     */
    public function init()
    {
        $filename = $this->getDirectory().DIRECTORY_SEPARATOR.$this->initFilename;

        if (File::exists($filename)) {
            File::requireOnce($filename);
        }
    }
}
