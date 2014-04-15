<?php defined('SYSPATH') or die('No direct script access.');

class Migration_Boom_20131126173900 extends Minion_Migration_Base
{

	public function up(Kohana_Database $db)
	{
		$db->query(null, "alter table page_versions add pending_approval boolean default false");
	}

	public function down(Kohana_Database $db)
	{
		$db->query(null, "alter table page_versions drop pending_approval");
	}
}