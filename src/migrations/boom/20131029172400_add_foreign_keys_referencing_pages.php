<?php

class Migration_Boom_20131029172400 extends Minion_Migration_Base
{

	public function up(Kohana_Database $db)
	{
		$db->query(null, "SET FOREIGN_KEY_CHECKS=0;");

		$db->query(null, "alter table page_versions add foreign key (page_id) references pages(id) on delete cascade on update cascade");
		$db->query(null, "alter table page_urls add foreign key (page_id) references pages(id) on delete cascade on update cascade");

		$db->query(null, "SET FOREIGN_KEY_CHECKS=0;");
	}

	public function down(Kohana_Database $db)
	{
	}
}