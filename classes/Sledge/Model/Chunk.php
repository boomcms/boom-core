<?php defined('SYSPATH') OR die('No direct script access.');
/**
* The chunk table is only useful with one of the related tables (chunk_text for example for a text chunk).  
* This class has a custom constructor which joins a chunk type table depending on the value of the type column.  
*
* @package	Sledge
* @category	Chunks
* @category	Models
* @author	Rob Taylor
* @copyright	Hoop Associates
*
*/
class Sledge_Model_Chunk extends ORM
{
	public $data;
	
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_columns = array(
		'id'		=>	'',
		'slotname'	=>	'',
		'type'	=>	'',
	);
	
	/**
	* Custom constructor for the slots tables.
	* Uses the type column to determine which table to join (if a record was loaded).
	* Joins the required slot type table and loads the slot data with the chunk data.
	*/
	public function _load_values( array $values)
	{
		parent::_load_values($values);
		
		if ($this->type)
		{
	        $this->data = ORM::factory("chunk_" . $this->type, $this->id);
		}
	}
	
	public function get_target()
	{
		switch ($this->type)
		{
			case 'asset':
				$target = $this->data->asset_id;
				break;
			case 'feature':
				$target = $this->data->target_page_id;
				break;
			case 'slideshow':
				$target = implode("-", $this->data->get_asset_ids());
				break;
			default:
				$target = 0;
		}
		
		return $target;
	}
	
	/**
	* Intercept setting the type column to define the relationship.
	*/
	public function set($column, $value)
	{
		if ($column == 'type')
		{
			$this->_has_one['data'] = array('model' => "chunk_" . $this->type);
			$this->data = ORM::factory('Chunk_' . ucfirst($value));
		}

		parent::set($column, $value);
	}
	
	public function show($template = NULL)
	{
		if (is_object($this->data))
		{
			return $this->data->show($template);
		}
		else
		{
			return NULL;
		}
	}
	
	public function save(Validation $validation = NULL)
	{
		if ($this->loaded() AND ($this->changed() OR $this->data->changed()))
		{
			$this->{$this->_primary_key} = NULL;
			$this->_loaded = FALSE;
			parent::save($validation);
			
			$this->data->chunk_id = $this->pk();
			$this->data->save($validation);
		}
		else
		{
			// Check that we're not saving an empty chunk.
			if ($this->type == 'feature' AND $this->data->target_page_id == 0)
			{
				return FALSE;
			}
			elseif ($this->type == 'asset' AND $this->data->asset_id == 0)
			{
				return FALSE;
			}
			elseif ($this->type == 'text' AND $this->data->text == 'Click on me to add some text here.')
			{
				return FALSE;
			}
						
			parent::save($validation);
			
			if ($this->type != '' AND is_object($this->data))
			{
				$this->data->chunk_id = $this->pk();
				$this->data->save();
			}
		}
		
		return TRUE;
	}
}