<?php defined('SYSPATH') or die('No direct script access.');

class Migration_Sledge_20121203164400 extends Minion_Migration_Base
{

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "create unique index pages_internal_name on pages(internal_name)");
		$db->query(NULL, "create index pages_external_indexing on pages(external_indexing)");
		$db->query(NULL, "create index pages_internal_indexing on pages(internal_indexing)");
		$db->query(NULL, "create index pages_visible_visible_from_visible_to_visible_in_nav on pages(visible, visible_from, visible_to, visible_in_nav)");
		$db->query(NULL, "create index pages_visible_visible_from_visible_to_visible_in_nav_cms on pages(visible, visible_from, visible_to, visible_in_nav_cms)");
		$db->query(NULL, "create index page_versions_stashed on page_versions(stashed)");
		$db->query(NULL, "create index page_versions_page_id_page_deleted on page_versions(page_id, page_deleted)");
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