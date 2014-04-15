<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Initial boom core structure.
 */
class Migration_Boom_20130613123700 extends Minion_Migration_Base
{

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(null, "
			CREATE TABLE IF NOT EXISTS `assets` (
			  `id` int(10) unsigned NOT null AUTO_INCREMENT,
			  `title` varchar(50) NOT null,
			  `description` text,
			  `width` smallint(5) unsigned DEFAULT null,
			  `height` smallint(5) unsigned DEFAULT null,
			  `filename` varchar(150) NOT null,
			  `visible_from` int(10) unsigned DEFAULT '0',
			  `type` varchar(100) DEFAULT null,
			  `filesize` int(10) unsigned DEFAULT '0',
			  `deleted` tinyint(1) DEFAULT '0',
			  `duration` int(10) unsigned DEFAULT null,
			  `encoded` tinyint(1) DEFAULT '1',
			  `views` int(10) unsigned DEFAULT null,
			  `uploaded_by` mediumint(8) unsigned DEFAULT null,
			  `uploaded_time` int(10) unsigned DEFAULT null,
			  `last_modified` int(10) unsigned DEFAULT null,
			  `thumbnail_asset_id` int(10) unsigned DEFAULT null,
			  PRIMARY KEY (`id`),
			  KEY `asset_v_rid` (`id`),
			  KEY `asset_v_deleted_visible_from_status` (`visible_from`),
			  KEY `asset_v_type` (`type`),
			  KEY `asset_v_deleted_filesize_asc` (`filesize`),
			  KEY `asset_v_deleted_filesize_desc` (`filesize`),
			  KEY `asset_v_deleted_title_desc` (`title`),
			  KEY `asset_v_deleted_title_asc` (`title`),
			  KEY `asset_v_rubbish` (`deleted`),
			  KEY `uploaded_by` (`uploaded_by`)
			) ENGINE=MyISAM AUTO_INCREMENT=8338 DEFAULT CHARSET=utf8;
		");

		$db->query(null, "
			CREATE TABLE IF NOT EXISTS `assets_tags` (
			  `asset_id` int(10) unsigned NOT null DEFAULT '0',
			  `tag_id` int(10) unsigned NOT null DEFAULT '0',
			  PRIMARY KEY (`asset_id`,`tag_id`),
			  KEY `assets_tags_tag_id` (`tag_id`),
			  KEY `assets_tags_asset_id` (`asset_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1;
		");

		$db->query(null, "
			CREATE TABLE IF NOT EXISTS `chunk_assets` (
			  `asset_id` smallint(5) unsigned DEFAULT null,
			  `caption` varchar(100) DEFAULT null,
			  `id` mediumint(8) unsigned NOT null AUTO_INCREMENT,
			  `title` varchar(20) DEFAULT null,
			  `url` varchar(255) DEFAULT null,
			  `slotname` varchar(50) DEFAULT null,
			  PRIMARY KEY (`id`),
			  KEY `asset_id` (`asset_id`)
			) ENGINE=InnoDB AUTO_INCREMENT=4810 DEFAULT CHARSET=utf8;
		");

		$db->query(null, "insert ignore into roles (name, description) values ('manage_assets', 'View the asset manager')");
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(null, "drop table assets");
		$db->query(null, "drop table assets_tags");
		$db->query(null, "drop table chunk_assets");
		$db->query(null, "delete from roles where name = 'manage_assets'");
	}
}
