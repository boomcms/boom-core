<?php

namespace BoomCMS\Core\Template;

use BoomCMS\Core\Theme\Theme;
use BoomCMS\Database\Models\Template as Model;
use Illuminate\Filesystem\Filesystem;

class Manager
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var Provider
     */
    protected $provider;

    public function __construct(Filesystem $filesystem, Provider $provider)
    {
        $this->filesystem = $filesystem;
        $this->provider = $provider;
    }

    public function createTemplateWithFilename($theme, $filename)
    {
        Model::create([
            'name'     => ucwords(str_replace('_', ' ', $filename)),
            'theme'    => $theme,
            'filename' => $filename,
        ]);
    }

    /**
     * Deletes templates where the filename points to an non-existent file.
     */
    public function deleteInvalidTemplates()
    {
        foreach ($this->getInvalidTemplates() as $template) {
            $template->delete();
        }
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
        $files = $this->filesystem->files($theme->getTemplateDirectory());
        $templates = [];

        if (is_array($files)) {
            foreach ($files as $file) {
                if (strpos($file, '.php') !== false) {
                    $file = str_replace($theme->getTemplateDirectory().'/', '', $file);
                    $templates[] = str_replace('.php', '', $file);
                }
            }
        }

        return $templates;
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
        $provider = new Provider();

        return $provider->findAll();
    }

    /**
     * Gets templates where the filename points to an non-existent file.
     */
    public function getInvalidTemplates()
    {
        $invalid = [];
        $templates = $this->getAllTemplates();

        foreach ($templates as $template) {
            if (!$template->fileExists()) {
                $invalid[] = $template;
            }
        }

        return $invalid;
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
        $template = $this->provider->findByThemeAndFilename($theme, $filename);

        return $template->loaded();
    }

    public function templateExistsWithFilename($filename)
    {
        $template = Factory::byFilename($filename);

        return $template->loaded();
    }
}
