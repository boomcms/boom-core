<?php

/**
* 
* Table name: message
* 
*************************** Table Columns ************************
****	Name				****	Data Type	****	Description					
****	id					****	integer		****	Primary key, auto increment
****	subject				****	string		****	The subject of the message.
****	body 				****	string		****	The body of the message.
****	sender_id			****	integer		****	Person ID of the user who sent the message.
****	receiver_id			****	integer		****	Person ID of the user who the message was sent to.
****	unread				****	boolean		****	Whether the message has been read by the receiver.
****	approval_request_id	****	integer		****	The ID of the approval request, if this is an approvals message
****	deleted				****	boolean		****	Whether the message has been deleted (true if it has).
****	time				****	integer		****	Unix timestamp representing when the message was sent.
******************************************************************
*
*
* @package Model
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Model_Message extends ORM {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_tablename = 'message';	

}

?>