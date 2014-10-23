<?php

namespace Boom\Person\Finder\Filter;

class GroupId extends \Boom\Finder\Filter
{
	protected $groupId;

	public function __construct($groupId)
	{
		$this->groupId = $groupId;
	}

	public function execute(\ORM $query)
	{
		return $query
			->join('people_groups', 'inner')
			->on('person_id', '=', 'id')
			->where('group_id', '=', $this->groupId);
	}

	public function shouldBeApplied()
	{
		return ctype_digit($this->groupId) && (int) $this->groupId > 0;
	}
}