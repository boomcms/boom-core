<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Initial sledge core structure.
 */
class Migration_Sledge_20121113114300 extends Minion_Migration_Base
{

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "drop table if exists tag_v");
		$db->query(NULL, "drop table if exists tag_mptt");
		$db->query(NULL, "alter table people_roles drop id");
		$db->query(NULL, "alter table people_roles add primary key(person_id, role_id, group_id, page_id)");
		$db->query(NULL, "alter table group_roles drop id");
		$db->query(NULL, "alter table group_roles add primary key(group_id, role_id, page_id)");
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