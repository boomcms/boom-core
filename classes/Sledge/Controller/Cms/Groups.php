<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Group controller
 * Pages for managing groups.
 *
 * @package	Sledge
 * @category	Controllers
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 */
class Sledge_Controller_Cms_Groups extends Sledge_Controller
{
	/**
	 *
	 * @var Model_Group	ORM object for the gorup we're editing.
	 */
	protected $group;

	public function before()
	{
		parent::before();

		if ( ! $this->auth->logged_in('manage_groups'))
		{
			throw new HTTP_Exception_403;
		}

		$this->group = ORM::factory('Group', $this->request->param('id'));
	}

	/**
	 * Add a permission to a group.
	 * Doesn't actually edit the group's permissions, displays a template allowing the user to select from available roles.
	 */
	public function action_add_permission()
	{
		// Subquery to get the roles currently assigned to the group.
		$current = DB::select('group_roles.role_id')
			->from('group_roles')
			->where('group_id', '=', $this->group->id);

		if ($page_id = $this->request->query('page') > 0)
		{
			$mptt = ORM::factory('Page_mptt', $page_id);

			$current
				->join('page_mptt', 'left')
				->on('group_roles.page_id', '=', 'page_mptt.id')
				->where('lft', '<=', $mptt->lft)
				->where('rgt', '>=', $mptt->rgt)
				->where('scope', '=', $mptt->scope);
		}

		$roles = ORM::factory('role')
			->join(array($current, 'group_roles'), 'left')
			->on('group_roles.role_id', '=', 'role.id')
			->where('group_roles.role_id', '=', NULL)
			->order_by('description', 'asc')
			->find_all();

		$this->template = View::factory('sledge/groups/permissions/add', array(
			'roles' => $roles
		));
	}

	/**
	 * Delete a group.
	 * The group to be deleted is specified in the URL
	 * e.g. /cms/groups/delete/1
	 *
	 * @uses Model_Group::delete()
	 */
	public function action_delete()
	{
		$this->_log("Deleted group " . $this->group->name);

		$this->group->delete();
	}

	/**
	 * Edit a group.
	 * Displays the edit group template. Changes are saved through the action_save() method.
	 */
	public function action_edit()
	{
		$general = $page = ORM::factory('Group_Role')
			->with('role')
			->where('group_id', '=', $this->group->id)
			->order_by('description', 'asc');

		$general = $general
			->where('page_id', '=', 0)
			->find_all();

		$page = $page
			->where('page_id', '!=', 0)
			->find_all();

		$this->template = View::factory('sledge/groups/edit', array(
			'general_permissions'	=>	$general,
			'page_permissions'		=>	$page,
			'group'				=>	$this->group,
		));
	}

	/**
	 * Edit the group's permissions for a point in the page tree.
	 *
	 * **Accepted GET variables:**
	 * Name		|	Type		|	Description
	 * ---------------|-----------------|---------------
	 * page		|	int		|	ID of a page to edit the permissions for.
	 *
	 */
	public function action_page_permissions()
	{
		$mptt = ORM::factory('Page_mptt', $this->request->query('page'));

		$permissions = ORM::factory('Group_Role')
			->with('role')
			->where('group_id', '=', $this->group->id)
			->where('page_id', '!=', 0)
			->find_all();

		$this->template = View::factory('sledge/groups/permissions/page', array(
			'permissions'	=>	$permissions,
			'group'		=>	$this->group,
		));
	}


	/**
	 * Save group details.
	 *
	 * **Accepted POST variables:**
	 * Name			|	Type		|	Description
	 * ---------------------|-----------------|---------------
	 * name			|	string	|	The new name of the group.
	 * permissions		|	string	|	List of numbers in the form "<action id> <page_id> <value>"
	 *
	 */
	public function action_save()
	{
		$name = $this->request->post('name');
		$permissions = (array) $this->request->post('permissions');
		$permissions = array_unique($permissions);

		// Update the group table.
		$this->group->name = $name;
		$this->group->save();

		// Log the action.
		$this->_log("Edited group " . $this->group->name . " (ID: " . $this->group->id . ")");

		// Get the current permissions for the group.
		$existing = DB::select('role_id', 'page_id', 'allowed')
			->from('group_roles')
			->where('group_id', '=', $this->group->id)
			->execute()
			->as_array();

		// Put it in the same format as the new permissions.
		array_walk($existing, function( & $val)
			{
				$val = implode(" ", $val);
			}
		);

		// Compare the new permissions with the existing permissions to find which have been removed and which are new.
		$added = array_diff($permissions, $existing);
		$removed = array_diff($existing, $permissions);

		// Insert new permissions
		foreach ($added as $a)
		{
			list($role_id, $page_id, $enabled) = explode(" ", $a);

			// Insert it into the group_roles table.
			DB::insert('group_roles', array('group_id', 'role_id', 'page_id', 'allowed'))
				->values(array(
					$this->group->id,
					$role_id,
					$page_id,
					$enabled
				))
				->execute();

			// Update the permissions for the people in this group.
			DB::insert('people_roles', array('person_id', 'group_id', 'role_id', 'page_id', 'allowed'))
				->values(array(
					DB::select('person_id')
						->from('people_groups')
						->where('group_id', '=', $this->group->id),
					$this->group->id,
					$role_id,
					$page_id,
					$enabled
				))
				->execute();
		}

		// Delete removed permissions.
		foreach ($removed as $r)
		{
			list($role_id, $page_id, $enabled) = explode(" ", $r);

			DB::delete('group_roles')
				->where('role_id', '=', $role_id)
				->where('group_id', '=', $this->group->id)
				->where('page_id', '=', $page_id)
				->execute();

			DB::delete('people_roles')
				->where('role_id', '=', $role_id)
				->where('group_id', '=', $this->group->id)
				->where('page_id', '=', $page_id)
				->execute();
		}

		$this->response->body($this->group->id);
	}
}
