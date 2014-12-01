<?php

namespace Boom\Template;

use \Boom\Page as Page;
use \Kohana as Kohana;
use \Model_Template as Model_Template;
use \View as View;
use DB;

class Template
{
    const DIRECTORY = 'site/templates/';

    /**
	 *
	 * @var Model_Template
	 */
    protected $model;

    public function __construct(\Model_Template $model)
    {
        $this->model = $model;
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

    public function getControllerName()
    {
        $parts = explode('_', $this->model->filename);

        foreach ($parts as & $part) {
            $part = ucfirst($part);
        }

        return implode('_', $parts);
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
