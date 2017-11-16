<?php

namespace BoomCMS\Asset;

use BoomCMS\Contracts\Models\Asset;
use BoomCMS\Contracts\Repositories\Album as AlbumRepository;
use BoomCMS\Contracts\Repositories\Asset as AssetRepository;
use BoomCMS\Contracts\Repositories\AssetVersion as AssetVersionRepository;
use BoomCMS\FileInfo\Contracts\FileInfoDriver;
use BoomCMS\FileInfo\Facade as FileInfo;
use Generator;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\Facades\Config;

class Importer
{
    /**
     * @var AlbumRepository
     */
    protected $albumRepository;

    /**
     * @var array
     */
    protected $albums = [];

    /**
     * @var array
     */
    protected $files;

    /**
     * @var FilesystemManager
     */
    protected $filesystems;

    /**
     * @var AssetRepository
     */
    protected $repository;

    /**
     * @var AssetVersionRepository
     */
    protected $versions;

    public function __construct(
        AssetRepository $repository,
        AssetVersionRepository $versions,
        AlbumRepository $albums,
        FilesystemManager $filesystems)
    {
        $this->repository = $repository;
        $this->versions = $versions;
        $this->albumRepository = $albums;
        $this->filesystems = $filesystems;
    }

    public function countFiles(string $disk): int
    {
        return count($this->getFiles($disk));
    }

    public function fileNeedsImport(string $disk, string $path): bool
    {
        return $this->isSupportedType($disk, $path) && !$this->versions->existsByFilesystemAndPath($disk, $path);
    }

    public function getAlbumForPath(string $path)
    {
        $albumName = basename(dirname($path));

        if (empty($albumName)) {
            return;
        }

        if (!isset($this->albums[$albumName])) {
            $this->albums[$albumName] = $this->albumRepository->findOrCreate($albumName);
        }

        return $this->albums[$albumName];
    }

    public function getFiles(string $disk): array
    {
        if (!isset($this->files[$disk])) {
            $this->files[$disk] = $this->getImportableFiles($disk);
        }

        return $this->files[$disk];
    }

    public function getImportableFiles(string $disk): array
    {
        $files = $this->filesystems->disk($disk)->allFiles();

        return array_filter($files, function (string $path) use ($disk) {
            return $this->fileNeedsImport($disk, $path);
        });
    }

    public function import(string $disk): Generator
    {
        $filesystem = $this->filesystems->disk($disk);

        foreach ($this->getFiles($disk) as $path) {
            $file = FileInfo::create($filesystem, $path);

            $asset = $this->importFile($disk, $file);
            $album = $this->getAlbumForPath($path);

            if ($album !== null) {
                $album->addAssets([$asset->getId()]);
            }

            yield $path;
        }
    }

    public function isSupportedType($disk, $path): bool
    {
        return in_array($this->filesystems->disk($disk)->mimeType($path), Config::get('boomcms.assets.supported'));
    }

    public function importFile(string $disk, FileInfoDriver $file): Asset
    {
        return $this->repository->createFromFile($disk, $file);
    }
}
