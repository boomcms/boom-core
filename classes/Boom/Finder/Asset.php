<?php

namespace Boom\Finder;

use \Boom\Finder as Finder;
use \ORM as ORM;

class Asset extends Finder
{
	/**
	 *
	 * @var array
	 */
	protected $_allowedOrderByColumns = array('last_modified', 'title', 'downloads', 'filesize', 'uploaded_time');

	/**
	 *
	 * @var Database_Query_Builder_Select
	 */
	protected $_query;

	public function __construct()
	{
		$this->_query = \ORM::factory('Asset');
	}

	public static function byId($id)
	{
		return \Boom\Asset::factory(new \Model_Asset($id));
	}

	public function find()
	{
		$assets = parent::find();

		return new \Boom\Asset\Result($assets);
	}

	/**
	 *
	 * @return array
	 */
	public function get_count_and_total_size()
	{
		$query = clone $this->_query;
		$query->reset();

		$this->_applyFilters($query);

		$result = $query
			->select(array(DB::expr('sum(filesize)'), 'filesize'))
			->select(array(DB::expr('count(*)'), 'count'))
			->find();

		return array(
			'count' => $result->get('count'),
			'filesize' => $result->get('filesize')
		);
	}

	public function setOrderBy($field, $direction = null)
	{
		in_array($field, $this->_allowedOrderByColumns) || $field = 'title';

		parent::setOrderBy($field, $direction);
	}
}