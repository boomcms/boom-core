<?php

namespace Boom\Person\Finder\Filter;

use Boom\Group as Group;

class Group extends \Boom\Finder\Filter
{
	protected $group;

	public function __construct(Group $group)
	{
		$this->group = $group;
	}

	public function execute(\ORM $query)
	{
		return $query
			->join('people_groups', 'inner')
			->on('person_id', '=', 'id')
			->where('group_id', '=', $this->group->getId());
	}
}