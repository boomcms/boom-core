<?php

namespace Boom\Template;

use \Boom\Page;
use \Kohana as Kohana;
use \Model_Template;
use \View;
use Request;
use DB;

class Template
{
    const DIRECTORY = 'site/templates/';

    /**
     * An associative array of chunks by type which are used by the template.
     *
     * @var array
     */
    protected $chunks = [];

    /**
     *
     * @var View
     */
    protected$view;

    /**
	 *
	 * @var Model_Template
	 */
    protected $model;

    public function __construct(\Model_Template $model)
    {
        $this->model = $model;
        $this->view = $this->getView();
    }

    /**
     * Dispay a page as HTML.
     * 
     *
     * @param \Boom\Page\Page $page
     * @param Request $request
     */
    public function asHtml(Page\Page $page, Request $request)
    {
        return $this->view;
    }

    /**
     * Dispay a page as JSON
     *
     * @param \Boom\Page\Page $page
     * @param Request $request
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
     * @param \Boom\Page\Page $page
     * @param Request $request
     * @param \Boom\Template\Response $response
     * @return string
     */
    public function asRss(Page\Page $page, Request $request)
    {
        return (new Page\RssFeed($this->page))->render();
    }

    public function countPages()
    {
        if ( ! $this->model->loaded()) {
            return 0;
        }

        $finder = new Page\Finder();
        $finder->addFilter(new Page\Finder\Filter\Template($this));

        return $finder->count();
    }

    /**
     *
     * @return \Boom\Template\Template
     */
    public function delete()
    {
        if ($this->loaded()) {
            $this->model->delete();
        }

        return $this;
    }

    public function fileExists()
    {
        return (bool) Kohana::find_file("views", $this->getFullFilename());
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
        return $this->model->description;
    }

    public function getFilename()
    {
        return $this->model->filename;
    }

    public function getFullFilename()
    {
        return static::DIRECTORY.$this->getFilename();
    }

    public function getId()
    {
        return $this->model->id;
    }

    public function getName()
    {
        return $this->model->name;
    }

    public function getTagGroupSuggestions()
    {
        $results = DB::select('group')
            ->from('tags')
            ->where('group', '!=', null)
            ->join('pages_tags', 'inner')
            ->on('pages_tags.tag_id', '=', 'tags.id')
            ->join('pages', 'inner')
            ->on('pages.id', '=', 'pages_tags.page_id')
            ->join('page_versions', 'inner')
            ->on('pages.id', '=', 'page_versions.page_id')
            ->where('page_versions.template_id', '=', $this->getId())
            ->distinct(true)
            ->execute()
            ->as_array('group');

        return array_keys($results);
    }

    public function getView()
    {
        return new View($this->getFullFilename());
    }

    public function loaded()
    {
        return $this->model->loaded();
    }
}
