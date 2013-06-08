<?php defined('SYSPATH') or die('No direct script access.');

class Migration_Boom_20130608102100 extends Minion_Migration_Base
{
	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "alter table people engine = InnoDB");
		$db->query(NULL, "create table auth_log (
			id bigint unsigned primary key auto_increment,
			person_id mediumint(8) unsigned,
			action tinyint not null,
			method varchar(10),
			ip int(10) not null,
			user_agent varchar(2000),
			time int(10) unsigned not null,
			index auth_log_person_id_time (person_id, time desc),
			index auth_log_ip(ip),
			foreign key (person_id) references people(id) on update cascade on delete cascade)");
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