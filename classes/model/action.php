<?php

/**
* Actions model
* Actions is part of the permissions system. It stores the available CMS actions and the permissions required to carry them out
* This is different to page permissions.
*
* Table name: actions
*
*************************** Table Columns ************************
****	Name			****	Data Type	****	Description					
****	id				****	integer		****	Primary key, auto increment
****	name			****	string		****	Action name. This is what is usually searched by to determine whether a person has permission. e.g. $person->can( 'manage people' ).
****	description		****	string		****	A user friendly description shown when displaying a person's available permissions.
******************************************************************
*
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
class Model_Action extends ORM {
	
	
}

?>