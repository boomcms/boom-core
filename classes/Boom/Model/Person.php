<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package	BoomCMS
 * @category	Models
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 */
class Boom_Model_Person extends ORM
{
	protected $_table_name = 'people';

	protected $_table_columns = array(
		'id'			=>	'',
		'name'		=>	'',
		'email'		=>	'',
		'enabled'		=>	'',
		'theme'		=>	'',
	);

	/**
	 * Properties to create relationships with Kohana's ORM
	 */
	protected $_has_many = array(
		'groups'		=> array(
			'model'	=> 'Group',
			'through'	=> 'people_groups',
		),
		'logs' => array(),
	);

	/**
	 * Returns whether the current person is allowed to perform the specified role.
	 *
	 * @param Model_Role $role
	 * @param Model_Page $page
	 *
	 * @return boolean
	 */
	public function is_allowed(Model_Role $role, Model_Page $page = NULL)
	{
		$query = DB::select('allowed')
			->from('people_roles')
			->where('person_id', '=', $person->id)
			->where('role_id', '=', $role->id);

		if ($page !== NULL)
		{
			$query
				->join('page_mptt', 'left')
				->on('people_roles.page_id', '=', 'page_mptt.id')
				->and_where_open()
					->where('lft', '<=', $page->mptt->lft)
					->where('rgt', '>=', $page->mptt->rgt)
					->where('scope', '=', $page->mptt->scope)
					->or_where_open()
						->where('people_roles.page_id', '=', 0)
					->or_where_close()
				->and_where_close();
		}

		$result = $query
			->execute()
			->as_array();

		return  ( ! empty($result) AND (boolean) $result[0]['allowed']);
	}
}