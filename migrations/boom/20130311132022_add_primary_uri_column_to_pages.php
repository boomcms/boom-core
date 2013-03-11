<?php defined('SYSPATH') or die('No direct script access.');

class Migration_Boom_20130311132022 extends Minion_Migration_Base
{
	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "alter table pages add primary_uri varchar(2048)");
		$db->query(NULL, "update pages inner join page_urls on pages.id = page_urls.page_id set primary_uri = location where is_primary = TRUE");
		$db->query(NULL, "create index pages_primary_uri on pages(primary_uri)");
	}

	public function down(Kohana_Database $db)
	{
		$db->query(NULL, "alter table pages drop primary_uri");
	}
}