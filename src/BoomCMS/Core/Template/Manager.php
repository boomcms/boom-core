<?php

namespace BoomCMS\Core\Template;

use BoomCMS\Core\Theme\Theme;
use BoomCMS\Repositories\Template as TemplateRepository;
use BoomCMS\Support\Str;
use Illuminate\Filesystem\Filesystem;

class Manager
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var TemplateRepository
     */
    protected $repository;

    public function __construct(Filesystem $filesystem, TemplateRepository $repository)
    {
        $this->filesystem = $filesystem;
        $this->repository = $repository;
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

        foreach ($this->findAvailableThemes() as $theme) {
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
     * @return array
     */
    public function findAvailableThemes()
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

        return $themes;
    }

    public function templateIsInstalled($theme, $filename)
    {
        $template = $this->repository->findByThemeAndFilename($theme, $filename);

        return $template !== null;
    }
}
