<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Initial boom core structure.
 */
class Migration_Boom_20130613124200 extends Minion_Migration_Base
{

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "
			CREATE TABLE IF NOT EXISTS`templates` (
				`id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(100) NOT NULL,
				`description` text,
				`filename` varchar(25) NOT NULL,
				PRIMARY KEY (`id`)
			  ) ENGINE=MyISAM AUTO_INCREMENT=83 DEFAULT CHARSET=utf8;
		");

		$db->query(NULL, "insert ignore into roles (name, description) values ('manage_templates', 'View the template manager')");
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(NULL, "drop table templates");
		$db->query(NULL, "delete from roles where name = 'manage_templates'");
	}
}
