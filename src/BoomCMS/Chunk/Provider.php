<?php

namespace BoomCMS\Chunk;

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Contracts\Models\PageVersion;
use BoomCMS\Support\Facades\Editor;
use Illuminate\Auth\AuthManager;

class Provider
{
    /**
     * @var AuthManager
     */
    protected $auth;

    public function __construct(AuthManager $auth)
    {
        $this->auth = $auth;
    }

    public function create(Page $page, $attrs)
    {
        $version = $page->addVersion();
        $attrs['page_vid'] = $version->getId();
        $attrs['page_id'] = $page->getId();
        $type = $attrs['type'];
        unset($attrs['type']);

        $modelName = 'BoomCMS\Database\Models\Chunk\\'.ucfirst($type);
        $model = $modelName::create($attrs);

        $className = 'BoomCMS\Chunk\\'.ucfirst($type);
        $attrs['id'] = $model->id;

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

        return Editor::isEnabled() && $this->auth->check('edit', $page);
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
        $className = 'BoomCMS\Chunk\\'.ucfirst($type);

        if ($page === null) {
            $page = Editor::getActivePage();
        } elseif ($page === 0) {
            // 0 was given as the page - this signifies a 'global' chunk not assigned to any page.
            $page = new Page();
        }

        $chunk = $this->find($type, $slotname, $page->getCurrentVersion());
        $attrs = $chunk ? $chunk->toArray() : [];

        return new $className($page, $attrs, $slotname, $this->allowedToEdit($page));
    }

    public function find($type, $slotname, PageVersion $version)
    {
        if (is_array($slotname)) {
            return $this->findMany($type, $slotname, $version);
        } else {
            return $this->findOne($type, $slotname, $version);
        }
    }

    public function findOne($type, $slotname, PageVersion $version)
    {
        $class = 'BoomCMS\Database\Models\Chunk\\'.ucfirst($type);

        return $version->getId() ?
            $class::getSingleChunk($version, $slotname)->first()
            : null;
    }

    public function findMany($type, array $slotnames, PageVersion $version)
    {
        $chunks = [];

        foreach ($slotnames as $slotname) {
            $chunks[] = $this->findOne($type, $slotname, $version);
        }

        return $chunks;
// TODO: fix loading multiple chunks in one go.
        $class = 'BoomCMS\Database\Models\Chunk\\'.ucfirst($type);

        return $class::latestEdit($version)
            ->where('c2.slotname', 'in', $slotnames)
//            ->with('target')
            ->get();
    }

    public function get($type, $slotname, Page $page)
    {
        $className = 'BoomCMS\Chunk\\'.ucfirst($type);

        $chunk = $this->find($type, $slotname, $page->getCurrentVersion());
        $attrs = $chunk ? $chunk->toArray() : [];

        return new $className($page, $attrs, $slotname, false);
    }

    public function load(Page $page, $chunks)
    {
        foreach ($chunks as $type => $slotnames) {
            $model = ucfirst($type);
            $class = "\BoomCMS\Chunk\\".$model;

            $models = $this->find($type, $slotnames, $page->getCurrentVersion());
            $found = [];

            foreach ($models as $m) {
                if ($m) {
                    $found[] = $m->slotname;
                    $chunks[$type][$m->slotname] = new $class($page, $m->toArray(), $m->slotname, $this->allowedToEdit($page));
                }
            }

            $not_found = array_diff($slotnames, $found);
            foreach ($not_found as $slotname) {
                $chunks[$type][$slotname] = new $class($page, [], $slotname, $this->allowedToEdit($page));
            }
        }

        return $chunks;
    }
}
