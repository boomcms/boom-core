<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Initial sledge core structure.
 */
class Migration_Sledge_20121113163000 extends Minion_Migration_Base
{

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "alter table page_uris rename to page_links");
		$db->query(NULL, "alter table page_links change uri location varchar(2048)");
		$db->query(NULL, "alter table page_links change primary_uri is_primary boolean");
		$db->query(NULL, "delete from roles where name like '%uri%'");
		$db->query(NULL, "insert into roles (name) values ('edit_page_links')");
		$db->query(NULL, "alter table page_versions change default_child_uri_prefix default_child_link_prefix varchar(2048)");
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