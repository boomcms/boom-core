<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Import people from centralised user database.
 */
class Migration_Boom_20130116161500 extends Minion_Migration_Base
{
	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "alter table logs change remotehost ip varchar(15)");
		$db->query(NULL, "alter table logs change description activity varchar(255)");
	}

	public function down(Kohana_Database $db) {}
}