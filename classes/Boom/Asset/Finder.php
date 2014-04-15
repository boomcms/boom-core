<?php

class Boom_Asset_Finder
{
	/**
	 *
	 * @var array
	 */
	protected $_allowed_order_by_columns = array('last_modified', 'title', 'downloads', 'filesize', 'uploaded_time');

	/**
	 *
	 * @var Database_Query_Builder_Select
	 */
	protected $_query;

	public function __construct()
	{
		$this->_query = DB::select()->from('assets');
	}

	/**
	 *
	 * @param array $tag_ids
	 * @return \Boom_Asset_Finder
	 */
	public function by_multiple_tags_ids(array $tag_ids)
	{

		return $this;
	}

	/**
	 *
	 * @param array $tag_ids
	 * @return \Boom_Asset_Finder
	 */
	public function by_tags(array $tag_ids)
	{
		$tag_ids = array_unique($tag_ids);
		$tag_count = count($tag_ids);

		if ($tag_count === 1)
		{
			$this->by_tags_single($tag_ids[0]);
		}
		elseif ($tag_count > 1)
		{
			$this->by_tags_multiple($tag_ids, $tag_count);
		}

		return $this;
	}

	/**
	 *
	 * @param integer $tag_id
	 * @return \Boom_Asset_Finder
	 */
	public function by_tags_single($tag_id)
	{
		if ($tag_id > 0)
		{
			$this->_join_tags();
			$this->_query->where('t1.tag_id', '=', $tag_id);
		}

		return $this;
	}

	/**
	 *
	 * @param array $tag_ids
	 * @param integer $tag_count
	 * @return \Boom_Asset_Finder
	 */
	public function by_tags_multiple(array $tag_ids, $tag_count = NULL)
	{
		$this->_join_tags();

		$tag_count !== NULL OR $tag_count = count($tag_ids);

		$this->_query
			->join(array('assets_tags', 't2'), 'inner')
			->on("t1.asset_id", '=', "t2.asset_id")
			->where('t2.tag_id', 'IN', $tag_ids)
			->group_by("t1.asset_id")
			->having(DB::expr('count(distinct t2.tag_id)'), '>=', $tag_count);

		return $this;
	}

	/**
	 *
	 * @param string $title
	 * @return \Boom_Asset_Finder
	 */
	public function by_title($title)
	{
		if ($title)
		{
			$this->_query->where('title', 'like', "%$title%");
		}

		return $this;
	}

	/**
	 *
	 * @param string $text
	 * @return \Boom_Asset_Finder
	 */
	public function by_title_or_description($text)
	{
		if ($text)
		{
			$this->_query
				->and_where_open()
				->where('title', 'like', "%$text%")
				->or_where('description', 'like', "%$text%")
				->and_where_close();
		}

		return $this;
	}

	/**
	 *
	 * @param mixed $type
	 * @return \Boom_Asset_Finder
	 */
	public function by_type($types)
	{
		if ( ! is_array($types))
		{
			$types = array($types);
		}

		foreach ($types as & $type)
		{
			if ( ! is_int($type) AND ! ctype_digit($type))
			{
				$type = constant('Boom_Asset::' . strtoupper($type));
			}
		}

		$this->_query->where('assets.type', 'in', $types);

		return $this;
	}


	public function get_assets($limit = NULL, $offset = NULL)
	{
		return $this->_query
			->select('assets.*')
			->limit($limit)
			->offset($offset)
			->as_object('Model_Asset')
			->execute();
	}

	/**
	 *
	 * @return integer
	 */
	public function get_count()
	{
		$query = clone $this->_query;
		$result = $query
			->select(array(DB::expr('count(*)'), 'count'))
			->execute();

		return $result->get('count');
	}

	/**
	 *
	 * @return array
	 */
	public function get_count_and_total_size()
	{
		$query = clone $this->_query;
		$result = $query
			->select(array(DB::expr('sum(filesize)'), 'filesize'))
			->select(array(DB::expr('count(*)'), 'count'))
			->execute();

		return array(
			'count' => $result->get('count'),
			'filesize' => $result->get('filesize')
		);
	}

	protected function _join_tags()
	{
		$this->_query
			->join(array('assets_tags', 't1'), 'inner')
			->on('assets.id', '=', 't1.asset_id')
			->distinct(true);
	}

	/**
	 *
	 * @param string $column
	 * @param string $direction
	 * @return \Boom_Asset_Finder
	 */
	public function order_by($column, $direction)
	{
		$direction === 'asc' OR $direction = 'desc';
		in_array($column, $this->_allowed_order_by_columns) OR $column = 'title';

		$this->_query->order_by($column, $direction);

		return $this;
	}
}