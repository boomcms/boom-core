<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Initial boom core structure.
 */
class Migration_Boom_20121008111800 extends Minion_Migration_Base
{

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "
			CREATE TABLE `chunk_features` (
			  `target_page_id` smallint(5) unsigned NOT NULL,
			  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
			  `slotname` varchar(50) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `target_page_id` (`target_page_id`)
			) ENGINE=InnoDB AUTO_INCREMENT=4944 DEFAULT CHARSET=utf8;
		");

		$db->query(NULL, "
			CREATE TABLE `chunk_linkset_links` (
			  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
			  `target_page_id` mediumint(8) unsigned DEFAULT NULL,
			  `chunk_linkset_id` smallint(5) unsigned NOT NULL,
			  `url` varchar(255) DEFAULT NULL,
			  `title` varchar(100) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `linksetlinks_chunk_linkset_id` (`chunk_linkset_id`)
			) ENGINE=InnoDB AUTO_INCREMENT=3065 DEFAULT CHARSET=utf8;
		");

		$db->query(NULL, "
			CREATE TABLE `chunk_linksets` (
				`title` varchar(20) NOT NULL,
				`id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
				`slotname` varchar(50) DEFAULT NULL,
				PRIMARY KEY (`id`)
			  ) ENGINE=InnoDB AUTO_INCREMENT=4111 DEFAULT CHARSET=utf8;
		");

		$db->query(NULL, "CREATE TABLE `chunk_slideshow_slides` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`asset_id` int(11) DEFAULT NULL,
			`url` varchar(100) DEFAULT NULL,
			`chunk_id` int(11) DEFAULT NULL,
			`caption` varchar(100) DEFAULT NULL,
			`title` varchar(20) DEFAULT NULL,
			PRIMARY KEY (`id`),
			KEY `slideshowimages_chunk_id` (`chunk_id`),
			KEY `asset_id` (`asset_id`)
		  ) ENGINE=InnoDB AUTO_INCREMENT=5374 DEFAULT CHARSET=utf8;
		");

		$db->query(NULL, "
			CREATE TABLE `chunk_slideshows` (
				`title` varchar(25) NOT NULL,
				`id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
				`slotname` varchar(50) DEFAULT NULL,
				PRIMARY KEY (`id`)
			  ) ENGINE=InnoDB AUTO_INCREMENT=5043 DEFAULT CHARSET=utf8;
		");

		$db->query(NULL, "
			CREATE TABLE `chunk_text_assets` (
			`chunk_id` mediumint(8) unsigned DEFAULT NULL,
			`asset_id` smallint(5) DEFAULT NULL,
			`position` smallint(5) unsigned DEFAULT NULL,
			UNIQUE KEY `chunk_text_assets` (`chunk_id`,`asset_id`),
			KEY `chunk_text_assets_chunk_id` (`chunk_id`)
		  ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
		");

		$db->query(NULL, "
			CREATE TABLE `chunk_texts` (
				`text` text NOT NULL,
				`id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
				`title` varchar(50) DEFAULT NULL,
				`slotname` varchar(50) DEFAULT NULL,
				PRIMARY KEY (`id`)
			  ) ENGINE=InnoDB AUTO_INCREMENT=6284 DEFAULT CHARSET=utf8;
		");

		$db->query(NULL, "
			CREATE TABLE `logs` (
			  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `ip` varchar(15) DEFAULT NULL,
			  `activity` varchar(255) DEFAULT NULL,
			  `note` text,
			  `person_id` smallint(5) unsigned DEFAULT NULL,
			  `time` int(10) unsigned DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `activitylog_person` (`person_id`)
			) ENGINE=MyISAM AUTO_INCREMENT=4762 DEFAULT CHARSET=utf8;
		");

		$db->query(NULL, "
			CREATE TABLE `page_chunks` (
				`page_vid` int(10) unsigned DEFAULT NULL,
				`chunk_id` int(10) unsigned DEFAULT NULL,
				`type` tinyint(3) unsigned DEFAULT NULL,
				KEY `page_chunks_page_vid_chunk_id_type` (`page_vid`,`chunk_id`,`type`)
			  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");

		$db->query(NULL, "
			CREATE TABLE `page_mptt` (
			  `lft` smallint(5) unsigned NOT NULL DEFAULT '0',
			  `rgt` smallint(5) unsigned NOT NULL,
			  `parent_id` int(11) DEFAULT NULL,
			  `lvl` tinyint(3) unsigned NOT NULL,
			  `scope` tinyint(3) unsigned NOT NULL DEFAULT '1',
			  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
			  PRIMARY KEY (`id`),
			  KEY `page_mptt_parent_id` (`parent_id`),
			  KEY `page_mptt_lft_rgt` (`lft`,`rgt`),
			  KEY `page_mptt_lvl` (`lvl`),
			  KEY `page_mptt_page_id_lft` (`lft`),
			  KEY `page_mptt_lft_scope_page_id` (`lft`,`scope`),
			  KEY `page_mptt_lft` (`lft`)
			) ENGINE=MyISAM AUTO_INCREMENT=1926 DEFAULT CHARSET=utf8;
		");

		$db->query(NULL, "
			CREATE TABLE `page_urls` (
			  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
			  `page_id` smallint(5) unsigned NOT NULL,
			  `location` varchar(2048) DEFAULT NULL,
			  `is_primary` tinyint(1) DEFAULT NULL,
			  `redirect` tinyint(1) DEFAULT '1',
			  PRIMARY KEY (`id`),
			  KEY `page_uri_page_id_primary_uri` (`page_id`,`is_primary`),
			  KEY `page_uri_uri` (`location`(255))
			) ENGINE=InnoDB AUTO_INCREMENT=2535 DEFAULT CHARSET=utf8;
		");

		$db->query(NULL, "
			CREATE TABLE `page_versions` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `page_id` smallint(5) unsigned DEFAULT NULL,
			  `template_id` tinyint(3) unsigned DEFAULT NULL,
			  `title` varchar(100) DEFAULT 'Untitled',
			  `edited_by` smallint(5) unsigned DEFAULT NULL,
			  `edited_time` int(10) unsigned DEFAULT NULL,
			  `page_deleted` tinyint(1) DEFAULT '0',
			  `feature_image_id` mediumint(8) unsigned DEFAULT NULL,
			  `published` tinyint(1) DEFAULT '0',
			  `embargoed_until` int(10) unsigned DEFAULT NULL,
			  `stashed` tinyint(1) DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `page_v_id_deleted` (`id`,`page_deleted`),
			  KEY `page_v_rid` (`page_id`),
			  KEY `page_v_aduit_time_rid_deleted` (`edited_time`,`page_id`,`page_deleted`),
			  KEY `page_v_title_rid_deleted` (`title`,`page_id`,`page_deleted`),
			  KEY `page_v_visible_in_leftnav_deleted` (`page_deleted`),
			  KEY `page_v_visible_in_leftnav_cms_deleted` (`page_deleted`),
			  KEY `page_versions_stashed` (`stashed`),
			  KEY `page_versions_page_id_page_deleted` (`page_id`,`page_deleted`),
			  KEY `edited_by` (`edited_by`),
			  KEY `page_versions_page_id_published_embargoed_until` (`page_id`,`published`,`embargoed_until`)
			) ENGINE=InnoDB AUTO_INCREMENT=8019 DEFAULT CHARSET=utf8;
		");

		$db->query(NULL, "
			CREATE TABLE `pages` (
			  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
			  `sequence` mediumint(8) unsigned DEFAULT '0',
			  `visible` tinyint(1) DEFAULT '0',
			  `visible_from` int(10) unsigned DEFAULT NULL,
			  `visible_to` int(10) unsigned NOT NULL DEFAULT '0',
			  `internal_name` varchar(64) DEFAULT NULL,
			  `external_indexing` tinyint(1) NOT NULL DEFAULT '1',
			  `internal_indexing` tinyint(1) NOT NULL DEFAULT '1',
			  `visible_in_nav` tinyint(1) NOT NULL DEFAULT '1',
			  `visible_in_nav_cms` tinyint(1) NOT NULL DEFAULT '1',
			  `children_visible_in_nav` tinyint(1) NOT NULL DEFAULT '1',
			  `children_visible_in_nav_cms` tinyint(1) NOT NULL DEFAULT '1',
			  `children_template_id` tinyint(3) unsigned DEFAULT NULL,
			  `children_url_prefix` varchar(2048) DEFAULT NULL,
			  `children_ordering_policy` tinyint(3) unsigned DEFAULT NULL,
			  `children_prompt_for_template` tinyint(1) DEFAULT '1',
			  `grandchild_template_id` tinyint(3) unsigned DEFAULT NULL,
			  `keywords` varchar(255) DEFAULT NULL,
			  `description` text,
			  `created_by` mediumint(8) unsigned DEFAULT NULL,
			  `created_time` int(10) unsigned DEFAULT NULL,
			  `primary_uri` varchar(2048) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `pages_internal_name` (`internal_name`),
			  KEY `page_sequence` (`sequence`),
			  KEY `pages_external_indexing` (`external_indexing`),
			  KEY `pages_internal_indexing` (`internal_indexing`),
			  KEY `pages_visible_visible_from_visible_to_visible_in_nav` (`visible`,`visible_from`,`visible_to`,`visible_in_nav`),
			  KEY `pages_visible_visible_from_visible_to_visible_in_nav_cms` (`visible`,`visible_from`,`visible_to`,`visible_in_nav_cms`),
			  KEY `pages_primary_uri` (`primary_uri`(255))
			) ENGINE=InnoDB AUTO_INCREMENT=1926 DEFAULT CHARSET=utf8;
		");

		$db->query(NULL, "
			CREATE TABLE `pages_tags` (
			  `page_id` int(10) unsigned NOT NULL DEFAULT '0',
			  `tag_id` int(10) unsigned NOT NULL DEFAULT '0',
			  PRIMARY KEY (`page_id`,`tag_id`),
			  KEY `pages_tags_page_id` (`page_id`),
			  KEY `pages_tags_tag_id` (`tag_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1;
		");

		$db->query(NULL, "
			CREATE TABLE `tags` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(255) NOT NULL,
			  `type` tinyint(1) unsigned NOT NULL,
			  `slug_short` varchar(255) NOT NULL,
			  `slug_long` varchar(255) NOT NULL,
			  PRIMARY KEY (`id`),
			  KEY `tag_name_type` (`name`,`type`),
			  KEY `tag_type_path` (`type`),
			  KEY `tags_slug_short` (`slug_short`),
			  KEY `tags_slug_long` (`slug_long`)
			) ENGINE=MyISAM AUTO_INCREMENT=2737 DEFAULT CHARSET=utf8;
		");
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(NULL, 'drop table chunk_features');
		$db->query(NULL, "drop table chunk_linkset_links");
		$db->query(NULL, 'drop table chunk_linksets');
		$db->query(NULL, 'drop table chunk_slideshow_slides');
		$db->query(NULL, 'drop table chunk_slideshows');
		$db->query(NULL, 'drop table chunk_text_assets');
		$db->query(NULL, 'drop table chunk_texts');
		$db->query(NULL, "drop table logs");
		$db->query(NULL, "drop table page_chunks");
		$db->query(NULL, "drop table page_mptt");
		$db->query(NULL, "drop table page_urls");
		$db->query(NULL, "drop table page_versions");
		$db->query(NULL, "drop table pages");
		$db->query(NULL, "drop table pages_tags");
		$db->query(NULL, "drop table tags");
	}
}
