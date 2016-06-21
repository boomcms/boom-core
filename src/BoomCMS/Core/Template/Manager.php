<?php

namespace BoomCMS\Core\Template;

use BoomCMS\Repositories\Template as TemplateRepository;
use BoomCMS\Support\Str;
use BoomCMS\Theme\Theme;
use Illuminate\Cache\Repository as Cache;
use Illuminate\Filesystem\Filesystem;

class Manager
{
    /**
     * @var Cache
     */
    protected $cache;

    /**
     * Cache key for the installed themes.
     *
     * @var string
     */
    protected $cacheKey = 'installedThemes';

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var TemplateRepository
     */
    protected $repository;

    public function __construct(Filesystem $filesystem, TemplateRepository $repository, Cache $cache)
    {
        $this->filesystem = $filesystem;
        $this->repository = $repository;
        $this->cache = $cache;
    }

    public function createTemplateWithFilename($theme, $filename)
    {
        $this->repository->create([
            'name'     => Str::filenameToTitle($filename),
            'theme'    => $theme,
            'filename' => $filename,
        ]);
    }

    public function findAndInstallNewTemplates()
    {
        $installed = [];

        foreach ($this->getInstalledThemes() as $theme) {
            foreach ($this->findAvailableTemplates($theme) as $template) {
                if (!$this->templateIsInstalled($theme, $template)) {
                    $installed[] = [$theme, $template];
                    $this->createTemplateWithFilename($theme, $template);
                }
            }
        }

        return $installed;
    }

    /**
     * @param Theme $theme
     *
     * @return array
     */
    public function findAvailableTemplates(Theme $theme)
    {
        $files = $this->filesystem->files($theme->getTemplateDirectory());
        $templates = [];

        if (is_array($files)) {
            foreach ($files as $filename) {
                if (pathinfo($filename, PATHINFO_EXTENSION) === 'php') {
                    $templates[] = basename($filename, '.php');
                }
            }
        }

        return $templates;
    }

    /**
     * Create a cache of the themes which are available on the filesystem.
     *
     * @return array
     */
    public function findAndInstallThemes()
    {
        $theme = new Theme();
        $directories = $this->filesystem->directories($theme->getThemesDirectory());
        $themes = [];

        if (is_array($directories)) {
            foreach ($directories as $directory) {
                $themeName = basename($directory);
                $themes[] = new Theme($themeName);
            }
        }

        $this->cache->forever($this->cacheKey, $themes);

        return $themes;
    }

    /**
     * Retrives the installed themes from the cache.
     *
     * If the cahce entry doesn't exist then it is created.
     *
     * @return array
     */
    public function getInstalledThemes()
    {
        $installed = $this->cache->get($this->cacheKey);

        if ($installed !== null) {
            return $installed;
        }

        return $this->findAndInstallThemes();
    }

    public function templateIsInstalled($theme, $filename)
    {
        $template = $this->repository->findByThemeAndFilename($theme, $filename);

        return $template !== null;
    }
}
