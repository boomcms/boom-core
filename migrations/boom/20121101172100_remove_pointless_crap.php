<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Initial sledge core structure.
 */
class Migration_Sledge_20121101172100 extends Minion_Migration_Base
{

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "alter table page_versions drop pagetype_description");
		$db->query(NULL, "alter table page_versions drop pagetype_parent_rid");
		$db->query(NULL, "alter table page_versions drop children_pagetype_parent_rid");
		$db->query(NULL, "alter table page_versions drop enable_rss");
		$db->query(NULL, "delete from roles where name = 'view_pagetype_description'");
		$db->query(NULL, "delete from roles where name = 'edit_pagetype_description'");
		$db->query(NULL, "delete from roles where name = 'view_pagetype_parent_id'");
		$db->query(NULL, "delete from roles where name = 'view_enable_rss'");
		$db->query(NULL, "delete from roles where name = 'edit_enable_rss'");
		$db->query(NULL, "drop table chunk_tag");
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