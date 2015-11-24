<?php

namespace BoomCMS\Console\Commands;

use BoomCMS\Core\Template\Manager as TemplateManager;
use Illuminate\Console\Command;
use PDOException;

class InstallTemplates extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'boomcms:installTemplates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Searches the themes directory for new templates and installs them.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire(TemplateManager $manager)
    {
        try {
            $installed = $manager->findAndInstallNewTemplates();

            if (count($installed)) {
                foreach ($installed as $i) {
                    list($theme, $template) = $i;

                    $this->info("Installed $template in theme $theme");
                }

                $this->call('vendor:publish', ['--force']);
            } else {
                $this->info('No templates to install');
            }
        } catch (PDOException $e) {
            $this->info('Unable to install templates: '.$e->getMessage());
        }
    }
}
