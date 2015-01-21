<?php

class Migration_Boom_20131127131300 extends Minion_Migration_Base
{

	public function up(Kohana_Database $db)
	{
		$db->query(null, "create index page_versions_pending_approval on page_versions(pending_approval)");
	}

	public function down(Kohana_Database $db)
	{
		$db->query(null, "drop index page_versions_pending_approval");
	}
}