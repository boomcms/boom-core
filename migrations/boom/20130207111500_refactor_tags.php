<?php defined('SYSPATH') or die('No direct script access.');

class Migration_Boom_20130207111500 extends Minion_Migration_Base
{
	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "create table pages_tags (page_id int unsigned, tag_id int unsigned, primary key(page_id, tag_id))");
		$db->query(NULL, "create table assets_tags (asset_id int unsigned, tag_id int unsigned, primary key(asset_id, tag_id))");

		$db->query(NULL, "insert into pages_tags (page_id, tag_id) select object_id, tag_id from tags_applied where object_type = 2");
		$db->query(NULL, "insert into assets_tags (asset_id, tag_id) select object_id, tag_id from tags_applied where object_type = 1");

		for ($i = 0; $i < 3; $i++)
		{
			$db->query(NULL, "insert ignore into assets_tags (tag_id, asset_id) select parent_id, object_id from tags_applied inner join tags on tags_applied.tag_id = tags.id where object_type = 1 and parent_id != 0");
		}

		$db->query(NULL, "drop table tags_applied");
		$db->query(NULL, "alter table tags drop parent_id");
	}

	public function down(Kohana_Database $db) {}
}