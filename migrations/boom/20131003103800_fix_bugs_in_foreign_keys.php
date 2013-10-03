<?php defined('SYSPATH') or die('No direct script access.');

class Migration_Boom_20131003103800 extends Minion_Migration_Base
{

	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "SET FOREIGN_KEY_CHECKS=0;");

		$db->query(NULL, "alter table chunk_slideshow_slides drop foreign key chunk_slideshow_slides_ibfk_1");
		$db->query(NULL, "alter table chunk_slideshow_slides drop foreign key chunk_slideshow_slides_ibfk_2");
		$db->query(NULL, "alter table chunk_assets drop foreign key chunk_assets_ibfk_2");

		$db->query(NULL, "alter table chunk_slideshow_slides add foreign key (asset_id) references assets(id) on delete cascade on update cascade");
		$db->query(NULL, "alter table chunk_slideshow_slides add foreign key (chunk_id) references chunk_slideshows(id) on delete cascade on update cascade");
		$db->query(NULL, "alter table chunk_assets add foreign key (asset_id) references assets(id) on delete cascade on update cascade");

		$db->query(NULL, "SET FOREIGN_KEY_CHECKS=1;");
	}

	public function down(Kohana_Database $db)
	{
	}
}