<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Initial boom core structure.
 */
class Migration_Boom_20121011120000 extends Minion_Migration_Base
{

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "ALTER table actions rename to roles");
		$db->query(NULL, "ALTER table roles drop component");
		$db->query(NULL, "ALTER table permissions change action_id role_id int unsigned");
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(NULL, "ALTER table roles RENAME TO actions");
		$db->query(NULL, "ALTER table permissions change role_id action_id int unsigned");
	}
}
