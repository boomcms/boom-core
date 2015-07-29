<?php

namespace BoomCMS\Core\Chunk;

use BoomCMS\Core\Auth\Auth;
use BoomCMS\Core\Editor\Editor;
use BoomCMS\Core\Page\Page;
use BoomCMS\Core\Page\Version;

class Provider
{
    /**
     *
     * @var Auth
     */
    protected $auth;

    /**
     *
     * @var Editor
     */
    protected $editor;

    public function __construct(Auth $auth, Editor $editor)
    {
        $this->auth = $auth;
        $this->editor = $editor;
    }

    public function create(Page $page, $attrs)
    {
        $version = $page->addVersion();
        $attrs['page_vid'] = $version->getId();
        $attrs['page_id'] = $page->getId();
        $type = $attrs['type'];
        unset($attrs['type']);

        $modelName = 'BoomCMS\Database\Models\Chunk\\' . ucfirst($type);
        $model = $modelName::create($attrs);

        $className = 'BoomCMS\Core\Chunk\\' . ucfirst($type);
        $attrs['id'] = $model->id;

        return new $className($page, $attrs, $attrs['slotname'], true);
    }

    /**
     * Returns whether the logged in user is allowed to edit a page
     *
     * @return boolean
     */
    public function allowedToEdit(Page $page)
    {
        if ( ! $page->loaded()) {
            return true;
        }

        return $this->editor->isEnabled() &&
            ($page->wasCreatedBy($this->auth->getPerson())
                || $this->auth->loggedIn("edit_page_content", $page)
            );
    }

    /**
     *
     * Returns a chunk object of the required type.
     *
     * @param  string    $type     Chunk type, e.g. text, feature, etc.
     * @param  string    $slotname The name of the slot to retrieve a chunk from.
     * @param  mixed     $page     The page the chunk belongs to. If not given then the page from the current request will be used.
     * @return BaseChunk
     */
    public function edit($type, $slotname, $page = null)
    {
        $className = 'BoomCMS\Core\Chunk\\' . ucfirst($type);

        if ($page === null) {
            $page = $this->editor->getActivePage();
        } elseif ($page === 0) {
            // 0 was given as the page - this signifies a 'global' chunk not assigned to any page.
            $page = new Page([]);
        }

        $chunk = $this->find($type, $slotname, $page->getCurrentVersion());
        $attrs = $chunk ? $chunk->toArray() : [];

        return new $className($page, $attrs, $slotname, $this->allowedToEdit($page));
    }

    public function find($type, $slotname, Version $version)
    {
        if (is_array($slotname)) {
            return $this->findMany($type, $slotname, $version);
        } else {
            return $this->findOne($type, $slotname, $version);
        }
    }

    public function findOne($type, $slotname, Version $version)
    {
        $class = 'BoomCMS\Database\Models\Chunk\\' . ucfirst($type);

        return $version->getId() ?
            $class::getSingleChunk($version, $slotname)->first()
            : null;
    }

    public function findMany($type, array $slotnames, Version $version)
    {
        $chunks = [];

        foreach ($slotnames as $slotname) {
            $chunks[] = $this->findOne($type, $slotname, $version);
        }

        return $chunks;
// TODO: fix loading multiple chunks in one go.
        $class = 'BoomCMS\Database\Models\Chunk\\' . ucfirst($type);

        return $class::latestEdit($version)
            ->where('c2.slotname', 'in', $slotnames)
//            ->with('target')
            ->get();
    }

    public function get($type, $slotname, Page $page)
    {
        $className = 'BoomCMS\Core\Chunk\\' . ucfirst($type);

        $chunk = $this->find($type, $slotname, $page->getCurrentVersion());
        $attrs = $chunk ? $chunk->toArray() : [];

        return new $className($page, $attrs, $slotname, false);
    }

    public function load(Page $page, $chunks)
    {
        foreach ($chunks as $type => $slotnames) {
            $model = ucfirst($type);
            $class = "\BoomCMS\Core\Chunk\\" . $model;

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
