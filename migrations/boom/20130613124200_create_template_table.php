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

		$db->query(NULL, "
			CREATE TABLE IF NOT EXISTS `assets_tags` (
			  `asset_id` int(10) unsigned NOT NULL DEFAULT '0',
			  `tag_id` int(10) unsigned NOT NULL DEFAULT '0',
			  PRIMARY KEY (`asset_id`,`tag_id`),
			  KEY `assets_tags_tag_id` (`tag_id`),
			  KEY `assets_tags_asset_id` (`asset_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1;
		");
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
