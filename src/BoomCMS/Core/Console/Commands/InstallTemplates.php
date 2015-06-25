<?php

namespace BoomCMS\Core\Console\Commands;

use BoomCMS\Core\Template;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Console\Command;

class InstallTemplates extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'boomcms:installTemplates';

    /**
     *
     * @var Template\Manager
     */
    protected $manager;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Searches the themes directory for new templates and installs them.';

    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();

        $this->manager = new Template\Manager($filesystem, new Template\Provider());
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $installed = $this->manager->findAndInstallNewTemplates();

        if (count($installed)) {
            foreach ($installed as $i) {
                list($theme, $template) = $i;

                $this->info("Installed $template in theme $theme");
            }
			
			$this->call('vendor:publish', ['--force']);
        } else {
            $this->info('No templates to install');
        }
    }
}
