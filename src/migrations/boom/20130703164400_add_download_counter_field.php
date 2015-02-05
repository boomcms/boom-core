<?php

class Migration_Boom_20130703164400 extends Minion_Migration_Base
{
	public function up(Kohana_Database $db)
	{
		$db->query(null, "alter table assets add downloads bigint unsigned default 0");

		$db->query(null, "create table asset_downloads (
			id bigint unsigned auto_increment primary key,
			asset_id int unsigned not null,
			time int unsigned
		)");
	}

	public function down(Kohana_Database $db)
	{
		$db->query(null, "drop table asset_downloads");
	}
}