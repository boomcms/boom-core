<?php defined('SYSPATH') or die('No direct script access.');

class Migration_Boom_20130111135400 extends Minion_Migration_Base
{
	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "update people_roles p1 inner join (select bit_and(allowed) as allowed, person_id, role_id from people_roles group by person_id, role_id) as p2 on p1.person_id = p2.person_id and p1.role_id = p2.role_id set p1.allowed = p2.allowed;");
		$db->query(NULL, "alter table people_roles drop group_id");
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function down(Kohana_Database $db)
	{
	}
}