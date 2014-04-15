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
		$db->query(null, "
			CREATE TABLE `auth_logs` (
				`id` bigint(20) unsigned NOT null AUTO_INCREMENT,
				`person_id` mediumint(8) unsigned DEFAULT null,
				`action` tinyint(4) NOT null,
				`method` varchar(10) DEFAULT null,
				`ip` int(10) NOT null,
				`user_agent` varchar(2000) DEFAULT null,
				`time` int(10) unsigned NOT null,
				PRIMARY KEY (`id`),
				KEY `auth_log_person_id_time` (`person_id`,`time`),
				KEY `auth_log_ip` (`ip`)
			  ) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=latin1;
		");

		$db->query(null, "
			CREATE TABLE `group_roles` (
				`group_id` mediumint(8) unsigned NOT null,
				`page_id` int(10) unsigned NOT null DEFAULT '0',
				`role_id` int(10) unsigned NOT null DEFAULT '0',
				`allowed` tinyint(1) DEFAULT null,
				PRIMARY KEY (`group_id`,`role_id`,`page_id`),
				KEY `permissions_role_id_action_id_where_id` (`group_id`,`role_id`,`page_id`),
				KEY `role_id` (`role_id`)
			  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		");

		$db->query(null, "
			CREATE TABLE `groups` (
				`id` smallint(5) unsigned NOT null AUTO_INCREMENT,
				`name` varchar(100) DEFAULT null,
				`deleted` tinyint(1) DEFAULT '0',
				PRIMARY KEY (`id`),
				KEY `groups_deleted` (`deleted`)
			  ) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
		");

		$db->query(null, "
			CREATE TABLE `password_tokens` (
			  `id` int(11) unsigned NOT null AUTO_INCREMENT,
			  `person_id` int(10) unsigned NOT null,
			  `token` varchar(40) NOT null,
			  `created` int(10) unsigned NOT null,
			  `expires` int(10) unsigned NOT null,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `uniq_token` (`token`),
			  KEY `password_tokens_person_id` (`person_id`),
			  KEY `password_tokens_expires` (`expires`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		");

		$db->query(null, "
			CREATE TABLE `people` (
			  `id` mediumint(8) unsigned NOT null AUTO_INCREMENT,
			  `name` varchar(255) DEFAULT null,
			  `email` varchar(255) DEFAULT null,
			  `enabled` tinyint(1) DEFAULT '1',
			  `password` varchar(60) DEFAULT null,
			  `failed_logins` tinyint(3) unsigned DEFAULT '0',
			  `locked_until` int(10) unsigned DEFAULT '0',
			  `avatar_id` int(10) unsigned DEFAULT null,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `people_email` (`email`)
			) ENGINE=InnoDB AUTO_INCREMENT=116 DEFAULT CHARSET=latin1;
		");

		$db->query(null, "
			CREATE TABLE `people_groups` (
			  `person_id` smallint(5) unsigned NOT null,
			  `group_id` smallint(5) unsigned NOT null,
			  UNIQUE KEY `person_group_person_id_group_id` (`person_id`,`group_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		");

		$db->query(null, "
			CREATE TABLE `people_roles` (
			  `person_id` int(10) unsigned NOT null,
			  `role_id` int(10) unsigned NOT null,
			  `group_id` smallint(5) unsigned NOT null,
			  `page_id` int(10) unsigned NOT null DEFAULT '0',
			  `allowed` tinyint(1) NOT null,
			  PRIMARY KEY (`person_id`,`role_id`,`group_id`,`page_id`),
			  KEY `role_id` (`role_id`),
			  KEY `people_roles_group_id_person_id` (`group_id`,`person_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1;
		");

		$db->query(null, "
			CREATE TABLE `roles` (
			  `id` smallint(5) unsigned NOT null AUTO_INCREMENT,
			  `name` varchar(100) DEFAULT null,
			  `description` varchar(100) DEFAULT null,
			  PRIMARY KEY (`id`),
			  KEY `actions_name` (`name`)
			) ENGINE=MyISAM AUTO_INCREMENT=81 DEFAULT CHARSET=utf8;
		");

		$db->query(null, "
			CREATE TABLE `user_tokens` (
			  `id` int(11) unsigned NOT null AUTO_INCREMENT,
			  `user_id` int(11) unsigned NOT null,
			  `user_agent` varchar(40) NOT null,
			  `token` varchar(40) NOT null,
			  `created` int(10) unsigned NOT null,
			  `expires` int(10) unsigned NOT null,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `uniq_token` (`token`),
			  KEY `fk_user_id` (`user_id`),
			  KEY `expires` (`expires`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		");

		$db->query(null, "INSERT INTO `roles` VALUES (2,'manage_people','People manager - view and edit'),(7,'p_edit_page','Page - edit'),(8,'p_delete_page','Page - delete'),(9,'p_add_page','Page - add'),(34,'p_edit_feature_image','Edit page - feature image'),(68,'view_reports','Reports - view'),(79,'manage_pages','Page manager - view and edit'),(49,'p_edit_page_template','Edit page - template'),(78,'p_edit_page_urls','Edit page - URLs'),(77,'p_edit_page_content','Edit page - content'),(76,'p_edit_page_admin','Edit settings - admin'),(75,'p_edit_page_children_advanced','Edit settings - children (advanced)'),(74,'p_edit_page_children_basic','Edit settings - children (basic)'),(73,'p_edit_page_search_advanced','Edit settings - search (advanced)'),(72,'p_edit_page_search_basic','Edit settings - search (basic)'),(71,'p_edit_page_navigation_advanced','Edit settings - navigation (advanced)'),(70,'p_edit_page_navigation_basic','Edit settings - navigation (basic)'),(67,'p_publish_page','Page - publish');");
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(null, "drop table auth_logs");
		$db->query(null, "drop table group_roles");
		$db->query(null, "drop table groups");
		$db->query(null, "drop table password_tokens");
		$db->query(null, "drop table people");
		$db->query(null, "drop table people_groups");
		$db->query(null, "drop table people_roles");
		$db->query(null, "drop table roles");
		$db->query(null, "drop table user_tokens");
	}
}