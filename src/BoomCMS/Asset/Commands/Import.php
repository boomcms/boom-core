<?php

namespace BoomCMS\Asset\Commands;

use BoomCMS\Asset\Importer;
use BoomCMS\Repositories\Album as AlbumRepository;
use BoomCMS\Repositories\Asset as AssetRepository;
use BoomCMS\Repositories\AssetVersion as AssetVersionRepository;
use Illuminate\Console\Command;
use Illuminate\Filesystem\FilesystemManager;

class Import extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports assets from the given filesystem';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'boomcms:import';

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'boomcms:import {filesystems* : The filesystem configurations to import assets from}';

    /**
     * @var Importer
     */
    protected $importer;

    public function __construct(
        AssetRepository $repository,
        AssetVersionRepository $versions,
        AlbumRepository $albums,
        FilesystemManager $filesystems)
    {
        parent::__construct();

        $this->importer = new Importer($repository, $versions, $albums, $filesystems);
    }

    public function fire()
    {
        $filesystems = $this->argument('filesystems');

        foreach ($filesystems as $filesystem) {
            try {
                $this->importDisk($filesystem);
            } catch (\InvalidArgumentException $e) {
                $this->error("Invalid filesystem: $filesystem");
            }
        }
    }

    protected function importDisk(string $disk)
    {
        $fileCount = $this->importer->countFiles($disk);
        $bar = $this->output->createProgressBar($fileCount);

        foreach ($this->importer->import($disk) as $file) {
            $bar->setMessage("Importing $file");
            $bar->advance();
        }

        $bar->finish();
    }
}
