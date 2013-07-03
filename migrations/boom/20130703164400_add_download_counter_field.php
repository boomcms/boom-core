<?php defined('SYSPATH') or die('No direct script access.');

class Migration_Boom_20130703164400 extends Minion_Migration_Base
{
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "alter table assets add downloads bigint unsigned default 0");

		$db->query(NULL, "create table asset_downloads (
			id bigint unsigned auto_increment primary key,
			asset_id int unsigned not null,
			time int unsigned
		)");
	}

	public function down(Kohana_Database $db)
	{
		$db->query(NULL, "drop table asset_downloads");
	}
}