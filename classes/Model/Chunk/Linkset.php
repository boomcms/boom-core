<?php

class Model_Chunk_Linkset extends ORM
{
	protected $_has_many = array(
		'links' => array('model' => 'Chunk_Linkset_Link', 'foreign_key' => 'chunk_linkset_id'),
	);

	protected $_links;

	protected $_table_columns = array(
		'id'		=>	'',
		'title'		=>	'',
		'slotname'	=>	'',
		'page_vid' => '',
	);

	protected $_table_name = 'chunk_linksets';

	public function copy($from_version_id)
	{
		$subquery = DB::select(DB::expr($this->id), 'target_page_id', 'url', 'chunk_linkset_links.title')
			->from('chunk_linkset_links')
			->join('chunk_linksets', 'inner')
			->on('chunk_linksets.id', '=', 'chunk_linkset_links.chunk_linkset_id')
			->where('slotname', '=', $this->slotname)
			->where('page_vid', '=', $from_version_id);

		DB::insert('chunk_linkset_links', array('chunk_linkset_id', 'target_page_id', 'url', 'title'))
			->select($subquery)
			->execute($this->_db);
	}

	public function create(Validation $validation = null)
	{
		parent::create($validation);

		$this->save_links();
		return $this;
	}

	public function filters()
	{
		return array(
			'title'	=> array(
				array('strip_tags'),
			)
		);
	}

	/**
	 * Sets or gets the linkset's links
	 *
	 */
	public function links($links = null)
	{
		if ($links === null)
		{
			// Act as getter.

			if ($this->_links === null)
			{
				$page = new Model\Page;

				$query = ORM::factory('Chunk_Linkset_Link')
					->join(array('pages', 'target'), 'left')
					->on('target_page_id', '=', 'target.id')
					->where('chunk_linkset_id', '=', $this->id);

				// Add the page to the select clause.
				foreach (array_keys($page->_object) as $column)
				{
					$query->select(array("target.$column", "target:$column"));
				}

				$this->_links = $query
					->find_all()
					->as_array();
			}

			return $this->_links;
		}
		else
		{
			// If the links are arrays of data then turn them into Chunk_Linkset_Links objects.
			foreach ($links as & $link)
			{
				if ( ! $link instanceof Linkset_Link)
				{
					$link = ORM::factory('Chunk_Linkset_Link')
						->values( (array) $link);
				}
			}

			$this->_links = $links;

			return $this;
		}
	}

	/**
	 * Persists link data to the database.
	 *
	 * @return \Boom_Model_Chunk_Linkset
	 */
	public function save_links()
	{
		// Remove all existing link.
		DB::delete('chunk_linkset_links')
			->where('chunk_linkset_id', '=', $this->id)
			->execute();

		// Loop through all the links.
		foreach ( (array) $this->_links as $link)
		{
			// Make the link belong to the current linkset.
			$link->chunk_linkset_id = $this->id;

			// Save the link.
			$link->save();
		}

		return $this;
	}
}