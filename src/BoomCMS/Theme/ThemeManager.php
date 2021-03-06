<?php

namespace BoomCMS\Theme;

use BoomCMS\Repositories\Template as TemplateRepository;
use BoomCMS\Support\Str;
use Illuminate\Cache\Repository as Cache;
use Illuminate\Filesystem\Filesystem;

class ThemeManager
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

    public function findAndInstallNewTemplates(): array
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

    public function findAvailableTemplates(Theme $theme): array
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
     */
    public function findAndInstallThemes(): array
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
     * Retrieves the installed themes from the cache.
     *
     * If the cache entry doesn't exist then it is created.
     */
    public function getInstalledThemes(): array
    {
        $installed = $this->cache->get($this->cacheKey);

        if ($installed !== null) {
            return $installed;
        }

        return $this->findAndInstallThemes();
    }

    public function templateIsInstalled($theme, $filename): bool
    {
        $template = $this->repository->findByThemeAndFilename($theme, $filename);

        return $template !== null;
    }
}
