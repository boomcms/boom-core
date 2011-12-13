<?php

/**
*
* The Tag, stuff, and metadata tables are sometimes used to create a sort of virtual table for added functionality.
* This class creates a model to represent such a virtual table.
* The class can be extended by other models when they have their data stored in these metatables.
*
* @package Metatable
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
* 
*/
class metatable_Model extends ORM {
	/**
	* @access private
	* @var object
	* Store the tag object which holds the table name in the tag table.
	*/
	private $_tag;

	/**
	*
	* Create a model representing the metatable
	* @param string $table_name The name of the table to be initialised.
	*
	*/
	protected function __construct( $table_name, $key = false ) {
		$this->_load_values( $key );
	}
	
	/**
	* Loads column values for a table row.
	*
	* @param int $key The primary key of the table row
	*/
	protected function _load_values( $key ) {
		$metadata = ORM::factory( 'metadata' )->find_by_item_tablename_and_item_rid( 'stuff', $key )->find_all();
		
		if (!$metadata->loaded) {
			$this->loaded = false;
			return false;
		} else {
			foreach( $metadata as $column ) {
				$colname = $metadata->key;
				$this->$colname = $metadata->value;				
			}			
		}		
	}
	
	// Emulate ORM's find_by etc. methods.
	public function __call( $method, $args ) {
		
		
	}
	
	/**
	* Delete a row from the 'table'
	*/
	public function delete() {
		
		
		
	}
	
	/**
	* Save a row to the 'table'
	*/
	public function save() {
		
		
	}
}

?>
