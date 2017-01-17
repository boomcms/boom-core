<?php

namespace BoomCMS\Chunk;

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Contracts\Models\PageVersion;
use BoomCMS\Database\Models\Chunk\BaseChunk as ChunkModel;
use BoomCMS\Database\Models\PageVersion as VersionModel;
use BoomCMS\Support\Facades\Editor;
use BoomCMS\Support\Facades\Router;
use Illuminate\Cache\Repository as Cache;
use Illuminate\Contracts\Auth\Access\Gate;

class Provider
{
    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var Gate
     */
    protected $gate;

    /**
     * Array of all chunk types.
     *
     * @var array
     */
    protected $types = [
        'asset',
        'calendar',
        'html',
        'text',
        'library',
        'linkset',
        'location',
        'slideshow',
        'text',
        'timestamp',
    ];

    /**
     * @param Gate  $gate
     * @param Store $cache
     */
    public function __construct(Gate $gate, Cache $cache)
    {
        $this->gate = $gate;
        $this->cache = $cache;
    }

    public function create(Page $page, $attrs)
    {
        $version = $page->addVersion();
        $attrs['page_vid'] = $version->getId();
        $attrs['page_id'] = $page->getId();
        $type = $attrs['type'];
        unset($attrs['type']);

        $modelName = $this->getModelName($type);
        $model = $modelName::create($attrs);

        $this->saveToCache($type, $attrs['slotname'], $version, $model);

        $className = $this->getClassName($type);
        $attrs['id'] = $model->id;

        $version->{VersionModel::ATTR_CHUNK_TYPE} = $type;
        $version->{VersionModel::ATTR_CHUNK_ID} = $model->id;
        $version->save();

        return new $className($page, $attrs, $attrs['slotname'], true);
    }

    /**
     * Returns whether the logged in user is allowed to edit a page.
     *
     * @return bool
     */
    public function allowedToEdit(Page $page = null)
    {
        if ($page === null) {
            return true;
        }

        return Editor::isEnabled() && $this->gate->allows('edit', $page);
    }

    /**
     * Returns a chunk object of the required type.
     *
     * @param string $type     Chunk type, e.g. text, feature, etc.
     * @param string $slotname The name of the slot to retrieve a chunk from.
     * @param mixed  $page     The page the chunk belongs to. If not given then the page from the current request will be used.
     *
     * @return BaseChunk
     */
    public function edit($type, $slotname, $page = null)
    {
        $className = $this->getClassName($type);

        if ($page === null) {
            $page = Router::getActivePage();
        }

        $model = $this->find($type, $slotname, $page->getCurrentVersion());
        $attrs = $model ? $model->toArray() : [];

        $chunk = new $className($page, $attrs, $slotname);

        return $chunk->editable($this->allowedToEdit($page));
    }

    /**
     * Find a chunk by page version, type, and slotname.
     *
     * @param string      $type
     * @param string      $slotname
     * @param PageVersion $version
     *
     * @return null|BaseChunk
     */
    public function find($type, $slotname, PageVersion $version)
    {
        $cached = $this->getFromCache($type, $slotname, $version);

        if ($cached !== false) {
            return $cached;
        }

        $class = $this->getModelName($type);

        $chunk = $version->getId() ?
            $class::getSingleChunk($version, $slotname)->first()
            : null;

        $this->saveToCache($type, $slotname, $version, $chunk);

        return $chunk;
    }

    /**
     * Find a chunk by it's ID.
     *
     * @param string $type
     * @param int    $chunkId
     *
     * @return ChunkModel
     */
    public function findById($type, $chunkId)
    {
        $model = $this->getModelName($type);

        return $model::find($chunkId);
    }

    public function get($type, $slotname, Page $page)
    {
        $className = $this->getClassName($type);

        $chunk = $this->find($type, $slotname, $page->getCurrentVersion());
        $attrs = $chunk ? $chunk->toArray() : [];

        return new $className($page, $attrs, $slotname);
    }

    /**
     * Get the cache key for given chunk parameters.
     *
     * @param type        $type
     * @param type        $slotname
     * @param PageVersion $version
     *
     * @return string
     */
    public function getCacheKey($type, $slotname, PageVersion $version)
    {
        return md5("$type-$slotname-{$version->getId()}");
    }

    /**
     * Returns the classname for a given chunk type.
     *
     * @param string $type
     *
     * @return string
     */
    public function getClassName($type)
    {
        return 'BoomCMS\Chunk\\'.ucfirst($type);
    }

    /**
     * Returns the classname for a model of the given chunk type.
     *
     * @param string $type
     *
     * @return string
     */
    public function getModelName($type)
    {
        return 'BoomCMS\Database\Models\Chunk\\'.ucfirst($type);
    }

    /**
     * Get a chunk from the cache.
     *
     * @param type        $type
     * @param type        $slotname
     * @param PageVersion $version
     *
     * @return mixed
     */
    public function getFromCache($type, $slotname, PageVersion $version)
    {
        $key = $this->getCacheKey($type, $slotname, $version);

        return $this->cache->get($key, false);
    }

    /**
     * Insert a chunk into a page.
     *
     * @param string    $type
     * @param string    $slotname
     * @param null|Page $page
     *
     * @return mixed
     */
    public function insert($type, $slotname, $page = null)
    {
        if ($page === null || $page === Router::getActivePage()) {
            return $this->edit($type, $slotname, $page);
        }

        return $this->get($type, $slotname, $page);
    }

    /**
     * Save a chunk to the cache.
     *
     * @param type            $type
     * @param type            $slotname
     * @param type            $version
     * @param null|ChunkModel $chunk
     */
    public function saveToCache($type, $slotname, $version, ChunkModel $chunk = null)
    {
        $key = $this->getCacheKey($type, $slotname, $version);

        $this->cache->forever($key, $chunk);
    }

    /**
     * Returns an array of chunks which have changed since a version.
     *
     * @param PageVersion $version
     *
     * @return array
     */
    public function since(PageVersion $version)
    {
        $chunks = [];

        foreach ($this->types as $type) {
            $className = $this->getModelName($type);

            $chunks[$type] = $className::getSince($version)->get();
        }

        return $chunks;
    }
}
