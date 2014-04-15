<?php defined('SYSPATH') or die('No direct script access.');

class Migration_Boom_20130913105800 extends Minion_Migration_Base
{
	public function up(Kohana_Database $db)
	{
		$db->query(null, "create index assets_downloads on assets(downloads)");
	}

	public function down(Kohana_Database $db)
	{
	}
}