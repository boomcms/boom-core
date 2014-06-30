<?php

class Migration_Boom_20130828163400 extends Minion_Migration_Base
{

	public function up(Kohana_Database $db)
	{
		$db->query(null, "
			CREATE TABLE `chunk_timestamps` (
			  `id` mediumint(8) unsigned NOT null AUTO_INCREMENT,
			  `timestamp` int(10) unsigned NOT null default 0,
			  `format` varchar(15) NOT null,
			  `slotname` varchar(50) DEFAULT null,
			  PRIMARY KEY (`id`),
			  KEY `timestamp` (`timestamp`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");
	}

	public function down(Kohana_Database $db)
	{
		$db->query(null, 'drop table chunk_timestamps');
	}
}