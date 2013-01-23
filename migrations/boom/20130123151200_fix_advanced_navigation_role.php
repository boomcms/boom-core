<?php defined('SYSPATH') or die('No direct script access.');

class Migration_Boom_20130123151200 extends Minion_Migration_Base
{
	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "update roles set description = 'Edit settings - navigation (advanced)', name = 'p_edit_page_navigation_advanced' where name = 'edit_page_navigation_advanced'");	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function down(Kohana_Database $db)
	{
	}
}