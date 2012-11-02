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

	/**
	* Local store of IDs of groups that this user belongs to.
	*
	* @var array
	* @access protected
	*/
	protected $_group_ids = NULL;

	/**
	* Assign the person to a group.
	* This can't be done using Kohana's $person->add because the tables are in different databases so it doesn't seem to work
	*
	* @param mixed $group Group ID or group object.
	* @return boolean True on success, FALSE on failure.
	*/
	public function add_group($group)
	{
		if ( ! is_object($group))
		{
			// Even though we only need the group ID we load the group to check that it exists.
			$group = ORM::factory('group', $group);
		}

		if ($group instanceof Model_Group AND $group->loaded())
		{
			try
			{
				ORM::factory('Person_group')
				->values( array('group_id' => $group->pk(), 'person_id' => $this->pk()))
				->create();
			}
			catch (Database_Exception $e)
			{
				if ($e->getCode() !== 1062)
				{
					throw $e;
				}
				else
				{
					return FALSE;
				}
			}

			return TRUE;
		}

		return FALSE;
	}

	public function email()
	{
		return $this->emailaddress;
	}
}
