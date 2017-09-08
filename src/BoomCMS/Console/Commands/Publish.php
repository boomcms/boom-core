<?php

namespace BoomCMS\Console\Commands;

use BoomCMS\Theme\ThemeManager;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Console\VendorPublishCommand;

class Publish extends VendorPublishCommand
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publishes files from the installed themes.';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'boomcms:publish';

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'boomcms:publish {--force : Overwrite any existing files.}';

    /**
     * @var ThemeManager
     */
    protected $manager;

    public function __construct(Filesystem $files, ThemeManager $manager)
    {
        parent::__construct($files);

        $this->manager = $manager;
    }

    /**
     * Publishes migrations and public files for all themes to their respective directories.
     *
     * The /public directory within all themes is copied to the application's public directory
     * to make them accessible to the webserver
     *
     * The migrations directory for all themes is copied to a shared migrations/boomcms directory
     * from where they can be run together
     *
     * @return mixed
     */
    public function fire()
    {
        $themes = $this->manager->findAndInstallThemes();

        foreach ($themes as $theme) {
            $directories = [
                $theme->getPublicDirectory()     => public_path('vendor/boomcms/themes/'.$theme->getName()),
                $theme->getMigrationsDirectory() => base_path('migrations/boomcms'),
            ];

            foreach ($directories as $from => $to) {
                if ($this->files->exists($from)) {
                    $this->publishDirectory($from, $to);
                }
            }
        }
    }
}
