<?php

namespace BoomCMS\Core\Template;

use BoomCMS\Core\Theme\Theme;
use BoomCMS\Repositories\Template as TemplateRepository;
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
            'name'     => ucwords(str_replace('_', ' ', $filename)),
            'theme'    => $theme,
            'filename' => $filename,
        ]);
    }

    public function findAndInstallNewTemplates()
    {
        $installed = [];

        foreach ($this->findInstalledThemes() as $theme) {
            foreach ($this->findAvailableTemplates($theme) as $template) {
                if (!$this->templateIsInstalled($theme, $template)) {
                    $installed[] = [$theme, $template];
                    $this->createTemplateWithFilename($theme, $template);
                }
            }
        }

        return $installed;
    }

    public function findAvailableTemplates(Theme $theme)
    {
        $filesArr = $this->filesystem->files($theme->getTemplateDirectory());

        if (!is_array($filesArr)){
            return [];
        }

        $templatesArr = [];
        foreach ($filesArr as $fileStr) {
            if (strpos($fileStr, '.php') === false){
            continue;
            } // if does not have .php skip it.

            $fileStr = str_replace($theme->getTemplateDirectory().'/', '', $fileStr); // remove the template directory
            $templatesArr[] = str_replace('.php', '', $fileStr); // remove .php
        }
        
        return $templatesArr;
    }

    public function findInstalledThemes()
    {
        $theme = new Theme();
        $themes = $this->filesystem->directories($theme->getThemesDirectory());

        if (is_array($themes)) {
            foreach ($themes as &$t) {
                $t = new Theme(str_replace($theme->getThemesDirectory().'/', '', $t));
            }
        }

        return $themes ?: [];
    }

    public function getAllTemplates()
    {
        return $this->repository->findAll();
    }

    public function getValidTemplates()
    {
        $valid = [];
        $templates = $this->getAllTemplates();

        foreach ($templates as $template) {
            if ($template->fileExists()) {
                $valid[] = $template;
            }
        }

        return $valid;
    }

    public function templateIsInstalled($theme, $filename)
    {
        $template = $this->repository->findByThemeAndFilename($theme, $filename);

        return $template !== null;
    }
}
