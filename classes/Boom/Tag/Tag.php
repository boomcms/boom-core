<?php

namespace Boom\Tag;

class Tag
{
    /**
	 *
	 * @var \Model_Tag
	 */
    protected $model;

    public function __construct(\Model_Tag $model)
    {
        $this->model = $model;
    }

    public function getGroup()
    {
        return $this->model->group;
    }

    public function getId()
    {
        return $this->model->id;
    }

    public function getName()
    {
        return $this->model->name;
    }

    public function loaded()
    {
        return $this->model->loaded();
    }
}
