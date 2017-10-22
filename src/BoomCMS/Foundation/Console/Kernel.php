<?php

namespace BoomCMS\Foundation\Console;

use BoomCMS\Asset\Commands\Import;
use BoomCMS\Theme\Commands;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * @var array
     */
    protected $commands = [
        Import::class,
        Commands\InstallTemplates::class,
        Commands\Publish::class,
    ];

    protected function schedule(Schedule $schedule)
    {
    }
}
