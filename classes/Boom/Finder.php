<?php defined('SYSPATH') OR die('No direct script access.');

abstract class Boom_Finder
{
	protected $_query;

	abstract protected function _apply_tag_filter(Model_Tag $tag);
	abstract public function sorted_by_title();
	abstract public function with_the_most_recent_first();

	/**
	 *
	 * @return Finder_Assets
	 */
	public static function assets()
	{
		return new Finder_Assets;
	}

	/**
	 *
	 * @return Finder_Pages
	 */
	public static function pages()
	{
		return new Finder_Pages;
	}

	/**
	 *
	 * @return Finder_Paginated
	 */
	public static function paginated(Finder $finder)
	{
		return new Finder_Paginated($finder);
	}

	protected function _tag_filter_should_be_applied(Model_Tag $tag)
	{
		return $tag->loaded();
	}

	public function count_matching()
	{
		$count_query = clone $this->_query;
		return $count_query->count_all();
	}

	public function filtered_by_month($month)
	{
		if ($month > 0)
		{
			$this->_query->where(DB::expr('month(from_unixtime(visible_from))'), '=', $month);
		}

		return $this;
	}

	public function filtered_by_year($year)
	{
		if ($year > 0)
		{
			$this->_query->where(DB::expr('year(from_unixtime(visible_from))'), '=', $year);
		}

		return $this;
	}

	public function get_results($limit = NULL)
	{
		if ($limit)
		{
			$this->_query->limit($limit);
		}

		return $this->_query->find_all();
	}

	public function sorted_by_property_and_direction($property, $direction)
	{
		$this->_query->order_by($property, $direction);

		return $this;
	}

	public function which_have_the_tag_named($tag_name)
	{
		$tag = new Model_Tag(array('name' => $tag_name));

		if ($this->_tag_filter_should_be_applied($tag))
		{
			$this->_apply_tag_filter($tag);
		}

		return $this;
	}
}