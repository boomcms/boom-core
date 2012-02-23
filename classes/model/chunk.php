<?php
/**
* Model for the chunk table.
* The chunk table is only useful with one of the related tables (chunk_text for example for a text chunk).
* This class has a custom constructor which joins a chunk type table depending on the value of the type column.
*
* Table name: chunk
*
*************************** Table Columns ************************
****	Name			****	Data Type	****	Description					
****	id				****	integer		****	Primary key, auto increment
****	slotname		****	string		****	Name of the slot that the chunk belongs to.
****	active_vid		****	integer		****	The ID of the related chunk table.
****	type			****	string		****	The type of chunk (text, feature, etc.)
******************************************************************
*
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Model_Chunk extends ORM
{
	public $data;
	
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'chunk';
	
	/**
	* Custom constructor for the slots tables.
	* Uses the type column to determine which table to join (if a record was loaded).
	* Joins the required slot type table and loads the slot data with the chunk data.
	*/
	public function _load_values( array $values )
	{
		parent::_load_values( $values );
		
		if ($this->type)
		{
	        $this->data = ORM::factory( "chunk_" . $this->type, $this->id );
		}
	}
	
	/**
	* Intercept setting the type column to define the relationship.
	*/
	public function set( $column, $value )
	{
		if ($column == 'type')
		{
			$this->_has_one['data'] = array( 'model' => "chunk_" . $this->type );
			$this->data = ORM::factory( 'chunk_' . $value );
		}

		parent::set( $column, $value );
	}
	
	public function show()
	{
		if (is_object( $this->data ))
		{
			return $this->data->show();
		}
		else
		{
			return null;
		}
	}
	
	public function save( Validation $validation = null )
	{
		if ($this->loaded() && ($this->changed() || $this->data->changed()))
		{
			$this->{$this->_primary_key} = null;
			$this->_loaded = false;
			parent::save( $validation );
			
			$this->data->chunk_id = $this->pk();
			$this->data->save( $validation );
		}
		else
		{
			// Check that we're not saving an empty chunk.
			if ($this->type == 'feature' && $this->data->target_page_id == 0)
			{
				return false;
			}
			else if ($this->type == 'asset' && $this->data->asset_id == 0)
			{
				return false;
			}
			else if ($this->type == 'text' && $this->data->text == 'Click on me to add some text here.')
			{
				return false;
			}
						
			parent::save( $validation );
			
			if ($this->type != '' && is_object( $this->data ))
			{
				$this->data->chunk_id = $this->pk();
				$this->data->save();
			}
		}
		
		return true;
	}
}

?>
