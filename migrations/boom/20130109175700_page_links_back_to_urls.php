<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Initial boom core structure.
 */
class Migration_Boom_20130109175700 extends Minion_Migration_Base
{

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "alter table page_links rename to page_urls");
		$db->query(NULL, "delete from roles where name like '%links%'");
		$db->query(NULL, "insert into roles (name) values ('edit_page_urls')");
		$db->query(NULL, "alter table pages change children_link_prefix children_url_prefix varchar(2048)");
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