<?php

namespace BoomCMS\Console\Commands;

use BoomCMS\Core\Template\Manager as TemplateManager;
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
     * @var Manager
     */
    protected $manager;

    public function __construct(Filesystem $files, TemplateManager $manager)
    {
        parent::__construct($files);

        $this->manager = $manager;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $themes = $this->manager->findAvailableThemes();

        foreach ($themes as $theme) {
            $directories = [
                $theme->getViewDirectory().DIRECTORY_SEPARATOR.'auth' => base_path('resources/views/auth'),
                $theme->getPublicDirectory() => public_path('vendor/boomcms/themes/'.$theme->getName()),
                $theme->getDirectory().'/migrations/' => base_path('/migrations/boomcms'),
            ];

            foreach ($directories as $from => $to) {
                if ($this->files->exists($from)) {
                    $this->publishDirectory($from, $to);
                }
            }
        }
    }
}
