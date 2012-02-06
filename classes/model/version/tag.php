<?php

/**
*
* The version table for tags.
*
* Table name: tag_v
* 
*************************** Table Columns ************************
****	Name								****	Data Type	****	Description		
****	id									****	integer		****	Primary key. auto increment.			
****	rid									****	integer		****	ID of the tag that this version belongs to.
****	name								****	string		****	The name of the tag.
****	hidden_from_tree					****	boolean		****	Whether to hide the tag from the tree I guess.
****	can_be_slot_perm					****	boolean		****	...
****	slug								****	string		****	...
****	child_ordering_policy_rid			****	integer		****	...
****	child_ordering_direction			****	integer		****	...
****	item_ordering_policy_rid			****	integer		**** 	The ordering policy applied to things which are tagged with this I guess.
****	item_ordering_direction				****	integer		****	ascending or descending I guess. This needs to be combined with the ordering policy using bitwise like pages now do.
****	effective_item_ordering_policy_rid	****	integer		****	God knows. Actually, he probably has no idea as well.
****	effective_item_ordering_direction	****	integer		****	Ditto.
****	audit_person						****	integer		****	Person ID of the user who created the version.
****	audit_time							****	integer		****	Unix timestamp of when the version was created.
****	deleted								****	boolean		****	Whether the group has been deleted.
******************************************************************
*
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Model_Version_Tag extends ORM {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'tag_v';
	protected $_has_one = array(
		'tag'				=> array( 'model' => 'tag', 'foreign_key' => 'id' ),
	);
}

?>