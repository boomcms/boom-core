<?php

class Migration_Boom_20130925110400 extends Minion_Migration_Base
{

	public function up(Kohana_Database $db)
	{
		$db->query(null, "SET FOREIGN_KEY_CHECKS=0;");

		$db->query(null, "alter table chunk_slideshow_slides change asset_id asset_id int(10) unsigned");
		$db->query(null, "alter table chunk_slideshows change id id int(8) unsigned auto_increment");
		$db->query(null, "alter table chunk_slideshow_slides change chunk_id chunk_id int(8) unsigned");
		$db->query(null, "alter table chunk_slideshow_slides add foreign key (asset_id) references assets(id) on delete cascade on update cascade");
		$db->query(null, "alter table chunk_slideshow_slides add foreign key (chunk_id) references chunk_slideshows(id) on delete cascade on update cascade");

		$db->query(null, "alter table chunk_assets change asset_id asset_id int(10) unsigned");
		$db->query(null, "alter table chunk_assets add foreign key (asset_id) references assets(id) on delete cascade on update cascade");

		$db->query(null, "SET FOREIGN_KEY_CHECKS=1;");
	}

	public function down(Kohana_Database $db)
	{
	}
}