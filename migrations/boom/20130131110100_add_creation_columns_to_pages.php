<?php defined('SYSPATH') or die('No direct script access.');

class Migration_Boom_20130131110100 extends Minion_Migration_Base
{
	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "alter table pages add created_by mediumint unsigned");
		$db->query(NULL, "alter table pages add created_time int unsigned");
		$db->query(NULL, "update pages inner join page_versions v1 on pages.id = v1.page_id inner join (select min(id) as id, page_id from page_versions group by page_id) as q on v1.page_id = q.page_id and v1.id = q.id set created_by = edited_by, created_time = edited_time");
	}

	public function down(Kohana_Database $db) {}
}