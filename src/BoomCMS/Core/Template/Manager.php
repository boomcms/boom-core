<?php

namespace BoomCMS\Core\Template;

use BoomCMS\Core\Models\Template as Model;
use Illuminate\Filesystem\Filesystem;

class Manager
{
    /**
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     *
     * @var Provider
     */
    protected $provider;

    protected $themesDir = 'boomcms/themes';

    public function __construct(Filesystem $filesystem, Provider $provider, $findAndInstall = true)
    {
        $this->filesystem = $filesystem;
        $this->provider = $provider;

        if ($findAndInstall) {
            $this->findAndInstallNewTemplates();
        }
    }

    public function createTemplateWithFilename($theme, $filename)
    {
        Model::create([
            'name' => ucwords(str_replace("_", " ", $filename)),
            'theme' => $theme,
            'filename' =>$filename,
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
        foreach ($this->findInstalledThemes() as $theme) {
            foreach ($this->findAvailableTemplates($theme) as $template) {
                if ( ! $this->templateIsInstalled($theme, $template)) {
                    $this->createTemplateWithFilename($theme, $template);
                }
            }
        }
    }

    public function findAvailableTemplates($theme)
    {
        $files = $this->filesystem->files($this->getThemeDirectory($theme));
        $templates = [];

        if (is_array($files)) {
            foreach ($files as $file) {
                if (strpos($file, '.php') !== false) {
                    $templates[] = str_replace('.php', '', $file);
                }
            }
        }

        return $templates;
    }

    public function findInstalledThemes()
    {
        $themes = $this->filesystem->directories($this->themesDir);

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
            if ( ! $template->fileExists()) {
                $invalid[] = $template;
            }
        }

        return $invalid;
    }

    public function getThemeDirectory($theme)
    {
        return $this->themesDir .
            DIRECTORY_SEPARATOR .
            $theme .
            DIRECTORY_SEPARATOR .
            'views' .
            DIRECTORY_SEPARATOR .
            'templates';
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
