<?php

class Migration_Boom_20130608113701 extends Minion_Migration_Base
{
	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "
			CREATE TABLE IF NOT EXISTS `password_tokens` (
			`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			`person_id` int UNSIGNED NOT NULL,
			`token` varchar(40) NOT NULL,
			`created` int(10) UNSIGNED NOT NULL,
			`expires` int(10) UNSIGNED NOT NULL,
			PRIMARY KEY (`id`),
			UNIQUE KEY `uniq_token` (`token`),
			KEY `password_tokens_person_id` (`person_id`),
			KEY `password_tokens_expires` (`expires`),
			FOREIGN KEY (`person_id`) REFERENCES `people` (`id`) ON DELETE CASCADE
		  ) DEFAULT CHARSET=utf8 ENGINE=MyISAM");
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