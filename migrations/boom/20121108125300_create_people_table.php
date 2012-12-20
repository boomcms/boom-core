<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Initial boom core structure.
 */
class Migration_Boom_20121108125300 extends Minion_Migration_Base
{
	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "create table people (
			id mediumint unsigned primary key auto_increment,
			name varchar(255),
			email varchar(255),
			enabled boolean default true,
			theme varchar(20))");
		$db->query(NULL, "create unique index people_email on people(email)");
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(NULL, "drop table people");
	}
}