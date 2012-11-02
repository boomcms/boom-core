<?php defined('SYSPATH') OR die('No direct script access.');

/**
* Sledge additions to Kohana's ORM.
* @package Sledge
* @category ORM
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
class Sledge_ORM extends ORM_Cache
{
	/**
	* Replacement to Kohana_ORM::_load_values();
	* Kohana_ORM::_load_values() uses array_key_exists which is slow than isset().
	*/
	protected function _load_values(array $values)
	{
		if (isset($values[$this->_primary_key]))
		{
            // Flag as loaded and valid
            $this->_loaded = $this->_valid = TRUE;

            // Store primary key
            $this->_primary_key_value = $values[$this->_primary_key];
		}
		else
		{
            // Not loaded or valid
            $this->_loaded = $this->_valid = FALSE;
		}

	    // Related objects
	    $related = array();

	    foreach ($values as $column => $value)
	    {
	        if (strpos($column, ':') === FALSE)
	        {
	            // Load the value to this model
	            $this->_object[$column] = $value;
	        }
	        else
	        {
	            // Column belongs to a related model
	            list ($prefix, $column) = explode(':', $column, 2);

	            $related[$prefix][$column] = $value;
	        }
	    }

	    if ( ! empty($related))
	    {
	        foreach ($related as $object => $values)
	        {
	            // Load the related objects with the values in the result
	            $this->_related($object)->_load_values($values);
	        }
	    }

	    if ($this->_loaded)
	    {
	        // Store the object in its original state
	        $this->_original_values = $this->_object;
	    }

	    return $this;
	}

	/**
	* Copy an object into a new object.
	* Resets the primary key so that the new object can be saved to the database as a new record.
	*
	* @return ORM
	*/
	public function copy()
	{
		$new = ORM::factory($this->object_name());
		foreach (array_keys($this->object()) as $column)
		{
			if ($column != $this->primary_key())
			{
				$new->$column = $this->$column;			
			}
		}
		
		return $new;
	}

	public static function factory($model, $id = NULL)
	{
		// Debugging code - 2012-08-08
		// There's some cases where - due to objects being cached instead of their IDs - ORM::factory() is being called with an ORM object as an ID.
		// This will allow finding those cases.
		if ($id instanceof ORM)
		{
			throw new Kohana_Exception("factory called with object as ID");
		}

		return parent::factory($model, $id);
	}
}