<?php defined('SYSPATH') or die('No direct script access.');

class Migration_Boom_20130311140400 extends Minion_Migration_Base
{
	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "create index page_versions_page_id_published_embargoed_until on page_versions(page_id, published, embargoed_until desc);");
		$db->query(NULL, "alter table page_verisons drop index page_v_published_rid;");
	}

	public function down(Kohana_Database $db)
	{
		$db->query(NULL, "alter table pages drop primary_uri");
	}
}