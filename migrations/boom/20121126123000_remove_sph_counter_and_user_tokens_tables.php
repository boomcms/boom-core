<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Initial sledge core structure.
 */
class Migration_Sledge_20121126123000 extends Minion_Migration_Base
{

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "drop table sph_counter");
		$db->query(NULL, "drop table user_tokens");
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