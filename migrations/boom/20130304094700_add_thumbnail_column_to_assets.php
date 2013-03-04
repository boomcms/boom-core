<?php defined('SYSPATH') or die('No direct script access.');

class Migration_Boom_20130304094700 extends Minion_Migration_Base
{
	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "alter table assets add thumbnail_asset_id int unsigned");
	}

	public function down(Kohana_Database $db)
	{
		$db->query(NULL, "alter table assets drop thumbnail_asset_id");
	}
}