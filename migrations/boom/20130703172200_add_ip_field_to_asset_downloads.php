<?php

class Migration_Boom_20130703172200 extends Minion_Migration_Base
{
	public function up(Kohana_Database $db)
	{
		$db->query(null, "alter table asset_downloads add ip int");
		$db->query(null, "alter table assets engine = InnoDB");
		$db->query(null, "alter table asset_downloads add constraint asset_downloads_asset_id foreign key (asset_id) references assets(id) on delete cascade");
		$db->query(null, "create index asset_downloads_ip_asset_id_time on asset_downloads(ip, asset_id, time desc)");
	}

	public function down(Kohana_Database $db)
	{
		$db->query(null, "alter table asset_downloads drop ip");
	}
}