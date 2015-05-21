<?php

namespace BoomCMS\Core\Template;

use BoomCMS\Core\Page;

use Illuminate\Support\Facades\View;

/**
 * Base template class.
 *
 * Templates should extend this class to provide custom methods for page events (save, delete, etc.) and dispaying pages in different formats.
 *
 */
class Template
{
    const DIRECTORY = 'site.templates.';

    /**
     * An associative array of chunks by type which are used by the template.
     *
     * @var array
     */
    protected $chunks = [];

    /**
     *
     * @var array
     */
    protected $data;

    /**
     * An array of tag groups which would usually be applied to pages using this template.
     *
     * These groups will appear in the page tag editor even if no tags in the group are applied to the page.
     *
     *
     * @var array
     */
    protected $suggestedTagGroups = [];

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->view = $this->getView();
    }

    /**
     * Dispay a page as HTML.
     *
     *
     * @param \Boom\Page\Page $page
     * @param Request         $request
     */
    public function asHtml(Page\Page $page, $request)
    {
        return $this->view;
    }

    /**
     * Dispay a page as JSON
     *
     * @param \Boom\Page\Page $page
     * @param Request         $request
     */
    public function asJson(Page\Page $page, Request $request)
    {
        return json_encode([
            'id' => $page->getId(),
            'title' => $page->getTitle(),
            'visible' => $page->isVisibleAtAnyTime(),
            'visible_to' => $page->getVisibleTo()->getTimestamp(),
            'visible_from' => $page->getVisibleFrom()->getTimestamp(),
            'parent' => $page->getParentId(),
            'bodycopy' => \Chunk::factory('text', 'bodycopy', $page)->text(),
            'standfirst' => \Chunk::factory('text', 'standfirst', $page)->text(),
        ]);
    }

    /**
     *  Dispay a page as RSS
     *
     *
     * @param  \Boom\Page\Page         $page
     * @param  Request                 $request
     * @param  \Boom\Template\Response $response
     * @return string
     */
    public function asRss(Page\Page $page, Request $request, \Response $response)
    {
        return $response->body((new Page\RssFeed($page))->render());
    }

    public function countPages()
    {
        if ( ! $this->loaded()) {
            return 0;
        }

        $finder = new Page\Finder();
        $finder->addFilter(new Page\Finder\Filter\Template($this));

        return $finder->count();
    }

    public function fileExists()
    {
        return View::exists($this->getFullFilename());
    }

    public function get($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    /**
     * Returns an associative array of chunks by type which are used by the template.
     *
     * @return array
     */
    public function getChunks()
    {
        return $this->chunks;
    }

    public function getDescription()
    {
        return $this->get('description');
    }

    public function getFilename()
    {
        return $this->get('filename');
    }

    public function getFullFilename()
    {
        return static::DIRECTORY . $this->getFilename();
    }

    public function getId()
    {
        return $this->get('id');
    }

    public function getName()
    {
        return $this->get('name');
    }

    public function getTagGroupSuggestions()
    {
        return $this->suggestedTagGroups;
    }

    public function getView()
    {
        return ($this->fileExists()) ? View::make($this->getFullFilename()) : View::make('boom::templates.default');
    }

    public function loaded()
    {
        return $this->getId() > 0;
    }

    /**
     * Called after the template is applied to a page.
     *
     * @param \Boom\Page\Page $page
     */
    public function onApplied(Page\Page $page) {}

    /**
     * Called after a page is created with this template.
     *
     * @param \Boom\Page\Page $page
     */
    public function onPageCreate(Page\Page $page) {}

    /**
     * Called before a page using this template is deleted.
     *
     * @param \Boom\Page\Page $page
     */
    public function onPageDelete(Page\Page $page) {}

    /**
     * Called after a page using this template has its title changed.
     *
     * @param \Boom\Page\Page $page
     * @param string
     * @param string
     */
    public function onPageRename(Page\Page $page, $oldTitle, $newTitle) {}

    /**
     * Called after a chunk is saved.
     *
     * @param \Boom\Page\Page $page
     * @param type            $chunk
     */
    public function onPageChunkSave(Page\Page $page, $chunk) {}

    /**
     * Called after a page using this template is saved.
     *
     * @param \Boom\Page\Page $page
     */
    public function onPageSave(Page\Page $page) {}
}
