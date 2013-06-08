<?php

class Migration_Boom_20130608113700 extends Minion_Migration_Base
{
	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "
			CREATE TABLE IF NOT EXISTS `user_tokens` (
			`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			`user_id` int(11) UNSIGNED NOT NULL,
			`user_agent` varchar(40) NOT NULL,
			`token` varchar(40) NOT NULL,
			`created` int(10) UNSIGNED NOT NULL,
			`expires` int(10) UNSIGNED NOT NULL,
			PRIMARY KEY (`id`),
			UNIQUE KEY `uniq_token` (`token`),
			KEY `fk_user_id` (`user_id`),
			KEY `expires` (`expires`),
			FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
		  ) DEFAULT CHARSET=utf8");
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