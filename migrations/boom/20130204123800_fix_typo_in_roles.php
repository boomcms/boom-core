<?php defined('SYSPATH') or die('No direct script access.');

class Migration_Boom_20130204123800 extends Minion_Migration_Base
{
	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "update roles set name = 'p_edit_page_children_advanced' where name = 'p_edit_page_children_advamced'");
	}

	public function down(Kohana_Database $db) {}
}