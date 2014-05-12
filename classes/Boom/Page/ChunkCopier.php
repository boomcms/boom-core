<?php

namespace Boom\Page;

/**
 * Class to copy chunks from one page version to another.
 * 
 */
class ChunkCopier
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
	 * @var \Model_Page_Version
	 */
	protected $_fromVersion;

	/**
	 *
	 * @var \Model_Page_Version
	 */
	protected $_toVersion;

	public function __construct(\Model_Page_Version $fromVersion, \Model_Page_Version $toVersion, array $exclude = null)
	{
		$this->_fromVersion = $fromVersion;
		$this->_toVersion = $toVersion;
		$this->_exclude = $exclude;
	}

	public function copyAll()
	{
		return $this
			->copyAssets()
			->copyFeatures()
			->copyLinksets()
			->copySlideshows()
			->copyText()
			->copyTimestamps()
			->copyTags();
	}
	
	public function copyAssets()
	{
		return $this->_doSimpleCopy('asset');
	}

	public function copyFeatures()
	{
		return $this->_doSimpleCopy('feature');
	}

	public function copyLinksets()
	{
		$this->_doSimpleCopy('linkset');
		$linksets = \ORM::factory('Chunk_Linkset')->where('page_vid', '=', $this->_toVersion->id)->find_all();

		foreach ($linksets as $linkset) {
			$linkset->copy($this->_fromVersion->id);
		}

		return $this;
	}

	public function copySlideshows()
	{
		$this->_doSimpleCopy('slideshow');

		$slideshows = \ORM::factory('Chunk_Slideshow')->where('page_vid', '=', $this->_toVersion->id)->find_all();

		foreach ($slideshows as $slideshow)
		{
			$slideshow->copy($this->_fromVersion->id);
		}

		return $this;
	}

	public function copyTags()
	{
		return $this->_doSimpleCopy('tag');
	}

	public function copyText()
	{
		return $this->_doSimpleCopy('text');
	}

	public function copyTimestamps()
	{
		return $this->_doSimpleCopy('timestamp');
	}

	protected function _doSimpleCopy($type)
	{
		$table = "chunk_$type".'s';
		$columns = $values = $this->_getColumnsForChunkType($type);
		\array_unshift($columns, 'page_vid');
		\array_unshift($values, \DB::expr($this->_toVersion->id));

		$subquery = \call_user_func_array(array('DB', 'select'), $values);
		$subquery
			->from($table)
			->where('page_vid', '=', $this->_fromVersion->id);

		if ( ! empty($this->_exclude[$type])) {
			$subquery->where('slotname', 'not in', $this->_exclude[$type]);
		}

		$count_query = clone $subquery;

		if (count($count_query->execute())) {
			\DB::insert($table, $columns)
				->select($subquery)
				->execute();
		}

		return $this;
	}

	protected function _getColumnsForChunkType($type)
	{
		$model_class = 'Chunk_'.ucfirst($type);
		$columns = \ORM::Factory($model_class)->object();
		$columns = \array_keys($columns);

		unset($columns['id']);
		unset($columns['page_vid']);

		foreach (array('id', 'page_vid') as $remove) {
			if (($key = \array_search($remove, $columns)) !== false) {
				unset($columns[$key]);
			}
		}

		return $columns;
	}
}