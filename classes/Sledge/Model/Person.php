<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package Sledge
 * @category Models
 *
 */
class Sledge_Model_Person extends Auth_Hoop_Model_Person implements Email_Recipient
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_has_many = array(
		'groups'		=> array(
			'model'	=> 'Group',
			'through'	=> 'people_groups',
		),
		'activities' => array('foreign_key' => 'person'),
	);

	public function email()
	{
		return $this->emailaddress;
	}
}
