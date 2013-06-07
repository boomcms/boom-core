<?php defined('SYSPATH') or die('No direct script access.');

class Migration_Boom_20130607143800 extends Minion_Migration_Base
{
	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "alter table people drop locked");
		$db->query(NULL, "alter table people add(locked_until int(10) unsigned default 0)");
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