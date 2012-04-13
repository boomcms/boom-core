<?php defined('SYSPATH') OR die('No direct script access.');

class Model_User_Token extends Sledge_Model_User_Token
{
	protected $_belongs_to = array( 'user' => array( 'model' => 'person', 'foreign_key' => 'user_id' ) );
	
	
}

?>