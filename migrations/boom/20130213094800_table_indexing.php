<?php defined('SYSPATH') or die('No direct script access.');

class Migration_Boom_20130213094800 extends Minion_Migration_Base
{
	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "create index assets_tags_tag_id on assets_tags(tag_id);");
		$db->query(NULL, "create index assets_tags_asset_id on assets_tags(asset_id)");
		$db->query(NULL, "create index pages_tags_page_id on pages_tags(page_id)");
		$db->query(NULL, "create index pages_tags_tag_id on pages_tags(tag_id);");
	}

	public function down(Kohana_Database $db) {}
}