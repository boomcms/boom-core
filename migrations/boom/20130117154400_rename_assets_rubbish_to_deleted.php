<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Import people from centralised user database.
 */
class Migration_Boom_20130117154400 extends Minion_Migration_Base
{
	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "alter table assets change rubbish deleted boolean default false");
	}

	public function down(Kohana_Database $db) {}
}