<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Initial sledge core structure.
 */
class Migration_Sledge_20121015134800 extends Minion_Migration_Base
{

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "create table person_roles (id bigint unsigned primary key auto_increment, person_id int unsigned not null, role_id int unsigned not null, group_id smallint unsigned not null, page_id int unsigned, allowed boolean not null)");
		$db->query(NULL, "create unique index person_roles_person_id_role_id_group_id on person_roles(person_id, role_id, group_id)");
		$db->query(NULL, "insert into person_roles (person_id, group_id, role_id, page_id, allowed) select person_group.person_id, permissions.group_id, permissions.role_id, permissions.where_id, permissions.value from person_group inner join permissions on person_group.group_id = permissions.group_id");
		$db->query(NULL, "alter table permissions rename group_roles");
		$db->query(NULL, "alter table group_roles change value allowed boolean");
		$db->query(NULL, "alter table group_roles drop where_type");
		$db->query(NULL, "alter table group_roles change where_id page_id int unsigned");
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
