<?php defined('SYSPATH') or die('No direct script access.');

class Migration_Boom_20130918100500 extends Minion_Migration_Base
{

	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "SET FOREIGN_KEY_CHECKS=0;");

		$db->query(NULL, "alter table chunk_assets add page_vid int(10) unsigned");
		$db->query(NULL, "alter table chunk_features add page_vid int(10) unsigned");
		$db->query(NULL, "alter table chunk_slideshows add page_vid int(10) unsigned");
		$db->query(NULL, "alter table chunk_texts add page_vid int(10) unsigned");
		$db->query(NULL, "alter table chunk_linksets add page_vid int(10) unsigned");
		$db->query(NULL, "alter table chunk_timestamps add page_vid int(10) unsigned");
		$db->query(NULL, "update chunk_assets inner join page_chunks on chunk_id = id and type = 1 set chunk_assets.page_vid = page_chunks.page_vid");
		$db->query(NULL, "update chunk_features inner join page_chunks on chunk_id = id and type = 2 set chunk_features.page_vid = page_chunks.page_vid");
		$db->query(NULL, "update chunk_linksets inner join page_chunks on chunk_id = id and type = 3 set chunk_linksets.page_vid = page_chunks.page_vid");
		$db->query(NULL, "update chunk_slideshows inner join page_chunks on chunk_id = id and type = 4 set chunk_slideshows.page_vid = page_chunks.page_vid");
		$db->query(NULL, "update chunk_texts inner join page_chunks on chunk_id = id and type = 5 set chunk_texts.page_vid = page_chunks.page_vid");
		$db->query(NULL, "update chunk_timestamps inner join page_chunks on chunk_id = id and type = 6 set chunk_timestamps.page_vid = page_chunks.page_vid");

		$db->query(NULL, "alter table chunk_assets add foreign key (page_vid) references page_versions (id) on delete cascade on update cascade, add unique index (slotname, page_vid);");
		$db->query(NULL, "alter table chunk_features add foreign key (page_vid) references page_versions (id) on delete cascade on update cascade, add unique index (slotname, page_vid);");
		$db->query(NULL, "alter table chunk_linksets add foreign key (page_vid) references page_versions (id) on delete cascade on update cascade, add unique index (slotname, page_vid);");
		$db->query(NULL, "alter table chunk_slideshows add foreign key (page_vid) references page_versions (id) on delete cascade on update cascade, add unique index (slotname, page_vid);");
		$db->query(NULL, "alter table chunk_texts add foreign key (page_vid) references page_versions (id) on delete cascade on update cascade, add unique index (slotname, page_vid);");
		$db->query(NULL, "alter table chunk_timestamps add foreign key (page_vid) references page_versions (id) on delete cascade on update cascade, add unique index (slotname, page_vid);");

		$db->query(NULL, "SET FOREIGN_KEY_CHECKS=1;");
	}

	public function down(Kohana_Database $db)
	{
	}
}