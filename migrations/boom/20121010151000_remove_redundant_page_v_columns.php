<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Initial sledge core structure.
 */
class Migration_Sledge_20121010151000 extends Minion_Migration_Base
{

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "ALTER table page_v DROP cache_duration");
		$db->query(NULL, "ALTER table page_v DROP approval_process_id");
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
