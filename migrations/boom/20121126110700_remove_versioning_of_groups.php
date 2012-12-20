<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Initial boom core structure.
 */
class Migration_Boom_20121126110700 extends Minion_Migration_Base
{

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "alter table groups add name varchar(100)");
		$db->query(NULL, "alter table groups add deleted boolean default false");
		$db->query(NULL, "update groups inner join group_versions on active_vid = group_versions.id set groups.id = group_versions.rid, groups.name = group_versions.name, groups.deleted = group_versions.deleted");
		$db->query(NULL, "alter table groups drop active_vid");
		$db->query(NULL, "drop table group_versions");
		$db->query(NULL, "create index groups_deleted on groups(deleted)");
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
