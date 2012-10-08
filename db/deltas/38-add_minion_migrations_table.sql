-- //

CREATE TABLE `minion_migrations` (
  `timestamp` varchar(14) NOT NULL,
  `description` varchar(100) NOT NULL,
  `group` varchar(100) NOT NULL,
  `applied` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`timestamp`,`group`),
  UNIQUE KEY `MIGRATION_ID` (`timestamp`,`description`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

insert into minion_migrations values (20121008111800, 'initial_sledge_structure', 'sledge', 1);
drop table changelog;

-- //@UNDO


-- //