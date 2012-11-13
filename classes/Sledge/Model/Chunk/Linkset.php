<?php defined('SYSPATH') OR die('No direct script access.');
/**
*
* @package	Sledge
* @category	Chunks
* @category	Models
* @author	Rob Taylor
* @copyright	Hoop Associates
*
*/
class Sledge_Model_Chunk_Linkset extends ORM
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_has_many = array(
		'links' => array('model' => 'Chunk_Linkset_Link', 'foreign_key' => 'chunk_linkset_id'),
	);
	protected $_load_with = array('links');
	protected $_table_columns = array(
		'id'		=>	'',
		'title'		=>	'',
		'slotname'	=>	'',
	);

	protected $_links = array();

	/**
	* Copy the slot
	*
	* @todo Move this to a ORM_Slot class - it's the same for all slots
	*/
	public function copy()
	{
		$new = parent::copy();
		$new->chunk = $this->chunk->copy();

		return $new;
	}

	/**
	* Gets or sets the linkset's links.
	*/
	public function links($links = NULL)
	{
		if ($links === NULL)
		{
			if ($this->_links == NULL)
			{
				$this->_links = $this->links->find_all()->as_array();
			}

			return $this->_links;
		}

		$this->_links = array();

		foreach ($links as $link)
		{
			$link = (array) $link;

			if ( ! isset($link['target_page_rid']))
			{
				$url = urldecode($link['uri']);
				$l = ORM::factory('linksetlink')->values(array('url' => $url, 'title' => $link['name']));
				$uris[] = $url;
				$this->_links[] = $l;
			}
			else
			{
				$l = ORM::factory('linksetlink')->values(array('target_page_id' => $link['target_page_rid']));
				$uris[] = $link['target_page_rid'];
				$this->_links[] = $l;
			}
		}
	}

	public function save(Validation $validation = NULL)
	{
		$return = parent::save($validation);

		// Save all the links.
		foreach ($this->_links as $link)
		{
			$link->chunk_linkset_id = $this->chunk_id;
			$link->save();
		}

		return $return;
	}
}
