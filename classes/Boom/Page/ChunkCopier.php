<?php

/**
 * Class to copy chunks from one page version to another.
 * 
 */
class Boom_Page_ChunkCopier
{
	/**
	 * Associative array of slotnames to exclude from copy. e.g.:
	 *
	 *	array(
	 *		'text' = array('bodycopy')
	 *	);
	 *
	 * @var array
	 */
	protected $_exclude;

	/**
	 *
	 * @var Model_Page_Version
	 */
	protected $_from_version;

	/**
	 *
	 * @var Model_Page_Version
	 */
	protected $_to_version;

	public function __construct(Model_Page_Version $from_version, Model_Page_Version $to_version, array $exclude = NULL)
	{
		$this->_from_version = $from_version;
		$this->_to_version = $to_version;
		$this->_exclude = $exclude;
	}

	public function copy_all()
	{
		return $this
			->copy_assets()
			->copy_features()
			->copy_linksets()
			->copy_slideshows()
			->copy_text()
			->copy_timestamps()
			->copy_tags();
	}
	
	public function copy_assets()
	{
		return $this->_do_simple_copy('asset');
	}

	public function copy_features()
	{
		return $this->_do_simple_copy('feature');
	}

	public function copy_linksets()
	{
		$this->_do_simple_copy('linkset');
		$linksets = ORM::factory('Chunk_Linkset')->where('page_vid', '=', $this->_to_version->id)->find_all();

		foreach ($linksets as $linkset)
		{
			$linkset->copy($this->_from_version->id);
		}

		return $this;
	}

	public function copy_slideshows()
	{
		$this->_do_simple_copy('slideshow');

		$slideshows = ORM::factory('Chunk_Slideshow')->where('page_vid', '=', $this->_to_version->id)->find_all();

		foreach ($slideshows as $slideshow)
		{
			$slideshow->copy($this->_from_version->id);
		}

		return $this;
	}

	public function copy_tags()
	{
		return $this->_do_simple_copy('tag');
	}

	public function copy_text()
	{
		return $this->_do_simple_copy('text');
	}

	public function copy_timestamps()
	{
		return $this->_do_simple_copy('timestamp');
	}

	protected function _do_simple_copy($type)
	{
		$table = "chunk_$type".'s';
		$columns = $values = $this->_get_columns_for_chunk_type($type);
		array_unshift($columns, 'page_vid');
		array_unshift($values, DB::expr($this->_to_version->id));

		$subquery = call_user_func_array(array('DB', 'select'), $values);
		$subquery
			->from($table)
			->where('page_vid', '=', $this->_from_version->id);

		if ( ! empty($this->_exclude[$type]))
		{
			$subquery->where('slotname', 'not in', $this->_exclude[$type]);
		}

		$count_query = clone $subquery;

		if (count($count_query->execute()))
		{
			DB::insert($table, $columns)
				->select($subquery)
				->execute();
		}

		return $this;
	}

	protected function _get_columns_for_chunk_type($type)
	{
		$model_class = 'Chunk_'.ucfirst($type);
		$columns = ORM::Factory($model_class)->object();
		$columns = array_keys($columns);

		unset($columns['id']);
		unset($columns['page_vid']);

		foreach (array('id', 'page_vid') as $remove)
		{
			if (($key = array_search($remove, $columns)) !== false)
			{
				unset($columns[$key]);
			}
		}

		return $columns;
	}
}