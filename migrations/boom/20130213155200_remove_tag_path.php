<?php defined('SYSPATH') or die('No direct script access.');

class Migration_Boom_20130213155200 extends Minion_Migration_Base
{
	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "update tags set name = path where type = 2");
		$db->query(NULL, "alter table tags drop path");
	}

	public function down(Kohana_Database $db) {}
}