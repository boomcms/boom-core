<?php

namespace Boom\Tag;

use \DB as DB;

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

	public function getId()
	{
		return $this->model->id;
	}

	public function getName()
	{
		return $this->model->name;
	}

	public function removeFromPages(array $pageIds)
	{
		if ( ! empty($pageIds)) {
			DB::delete('pages_tags')
				->where('tag_id', '=', $this->getId())
				->where('page_id', 'in', $pageIds)
				->execute();
		}

		return $this;
	}
}