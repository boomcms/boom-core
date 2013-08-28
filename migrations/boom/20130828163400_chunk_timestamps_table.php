<?php defined('SYSPATH') or die('No direct script access.');

class Migration_Boom_20130828163400 extends Minion_Migration_Base
{

	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "
			CREATE TABLE `chunk_timestamps` (
			  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
			  `timestamp` int(10) unsigned NOT NULL default 0,
			  `format` varchar(15) NOT NULL,
			  `slotname` varchar(50) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `timestamp` (`timestamp`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");
	}

	public function down(Kohana_Database $db)
	{
		$db->query(NULL, 'drop table chunk_timestamps');
	}
}