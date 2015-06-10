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

    /**
     *
     * @var Page
     */
    protected $defaultPage;

    public function __construct(Auth $auth, Editor $editor, Page $defaultPage)
    {
        $this->auth = $auth;
        $this->editor = $editor;
        $this->defaultPage = $defaultPage;
    }

    /**
     * Returns whether the logged in user is allowed to edit a page
     *
     * @return boolean
     */
    public function allowedToEdit(Page\Page $page)
    {
        if ( ! $page->loaded()) {
            return true;
        }

        return $this->editor->isEnabled() &&
            ($this->page->wasCreatedBy($this->auth->getPerson())
                || $this->auth->loggedIn("edit_page_content", $page)
            );
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
        $model = "Chunk_" . ucfirst($type);

        return $model::where('page_vid', '=', $version->id)
            ->with('target')
            ->where('slotname', '=', $slotname)
            ->first();
    }

    public function findMany($type, array $slotnames, Version $version)
    {
        $model = "Chunk_" . ucfirst($type);

        return $model::where('slotname', 'in', $slotnames)
            ->with('target')
            ->where('page_vid', '=', $version->id)
            ->get();
    }

    /**
     *
     * Returns a chunk object of the required type.
     *
     * @param string $type Chunk type, e.g. text, feature, etc.
     * @param string $slotname The name of the slot to retrieve a chunk from.
     * @param mixed	 $page The page the chunk belongs to. If not given then the page from the current request will be used.
     * @return  BaseChunk
     */
    public function view($type, $slotname, $page = null)
    {
        $className = ucfirst($type);

        if ($page === null) {
            $page = $this->defaultPage;
        } elseif ($page === 0) {
            // 0 was given as the page - this signifies a 'global' chunk not assigned to any page.
            $page = new Page([]);
        }

        $chunk = $this->find($type, $slotname, $page->getCurrentVersion());

        return new $className($page, $chunk, $slotname, $this->allowedToEdit($page));
    }
}