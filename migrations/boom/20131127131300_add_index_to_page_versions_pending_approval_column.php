<?php defined('SYSPATH') or die('No direct script access.');

class Migration_Boom_20131127131300 extends Minion_Migration_Base
{

	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "create index page_versions_pending_approval on page_versions(pending_approval)");
	}

	public function down(Kohana_Database $db)
	{
		$db->query(NULL, "drop index page_versions_pending_approval");
	}
}