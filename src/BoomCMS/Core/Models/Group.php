<?php

namespace BoomCMS\Core\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $_has_many = ['roles' => ['through' => 'group_roles']];

    protected $_table_columns = [
        'id'            =>    '',
        'name'        =>    '',
        'deleted'        =>    '',
    ];

    protected $table = 'groups';

    /**
	 * Delete a group.
	 *
	 * Groups are not really deleted.
	 * Instead the 'deleted' column is set to true.
	 * When marked as deleted a group will not appear in group lists and it's permissions will not effect the permissions of the people who belong to the group.
	 *
	 * We mark groups as deleted rather than deleting them completely so that mistakes can be reverted easily.
	 * If we actually deleted a group we'd lose the records of:
	 *
	 * * Which people belong to the group.
	 * * Which roles have been assigned to the group.
	 *
	 * This could cause a big headache if an important group was mistakenly deleted.
	 *
	 * @return \Boom_Model_Group
	 */
    public function delete()
    {
        // Delete the people_roles records for this group
        // so that this group doesn't influence people's permissions.
        DB::delete('people_roles')
            ->where('group_id', '=', $this->id)
            ->execute($this->_db);

        // Make the group as deleted.
        $this->deleted = true;

        // Save the changes.
        $this->update();

        // Return a cleared group model.
        return $this->clear();
    }

    /**
	 * Returns an array of the ID and name of all groups.
	 * The returned array is sorted alphabetically by name, A - Z.
	 *
	 * This function can be used to build a select box of groups, e.g.:
	 *
	 *	<?= Form::select('group_id', ORM::factory('Group')->names()) ?>
	 *
	 *
	 * Optionally an array of group names, or a Database_Query_Builder_Select object, can be given to exclude those groups from the results.
	 * This could be used to get the names of all groups that a person is not already a member of.
	 *
	 *	<?= Form::select('group_id', ORM::factory('Group')->names(array('Group name'))) ?>
	 *
	 *
	 * @param mixed $exclude
	 * @return array
	 *
	 * @throws InvalidArgumentException
	 */
    public function names($exclude = null)
    {
        // $exclude should be an array or DB select.
        if ($exclude !== null && ! (is_array($exclude) || $exclude instanceof Database_Query_Builder_Select)) {
            // Throw an exception.
            throw new InvalidArgumentException("Argument 1 for ".__CLASS__."::".__METHOD__." should be an array or instance of Database_Query_Builder_Select, ".tyepof($excluding). "given");
        }

        // Prepare the query
        $query = DB::select('id', 'name')
            ->from($this->_table_name)
            ->where('deleted', '=', false)
            ->order_by('name', 'asc');

        // Are we excluding any groups?
        if ($exclude !== null) {
            // Exclude these groups from the results.
            $query->where('id', 'NOT IN', $exclude);
        }

        // Run the query and return the results.
        return $query
            ->execute($this->_db)
            ->as_array('id', 'name');
    }

    public function rules()
    {
        return [
            'name' => [
                ['not_empty'],
            ],
        ];
    }
}
