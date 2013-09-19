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
		$db->query(NULL, "update chunk_assets inner join page_chunks on chunk_id = id set chunk_assets.page_vid = page_chunks.page_vid where type = 1 and page_chunks.page_vid = (select max(p1.page_vid) from page_chunks p1 where p1.chunk_id = chunk_assets.id and p1.type = page_chunks.type)");
		$db->query(NULL, "update chunk_features inner join page_chunks on chunk_id = id set chunk_features.page_vid = page_chunks.page_vid where type = 2 and page_chunks.page_vid = (select max(p1.page_vid) from page_chunks p1 where p1.chunk_id = chunk_features.id and p1.type = page_chunks.type)");
		$db->query(NULL, "update chunk_linksets inner join page_chunks on chunk_id = id set chunk_linksets.page_vid = page_chunks.page_vid where type = 3 and page_chunks.page_vid = (select max(p1.page_vid) from page_chunks p1 where p1.chunk_id = chunk_linksets.id and p1.type = page_chunks.type)");
		$db->query(NULL, "update chunk_slideshows inner join page_chunks on chunk_id = id set chunk_slideshows.page_vid = page_chunks.page_vid where type = 4 and page_chunks.page_vid = (select max(p1.page_vid) from page_chunks p1 where p1.chunk_id = chunk_slideshows.id and p1.type = page_chunks.type)");
		$db->query(NULL, "update chunk_texts inner join page_chunks on chunk_id = id set chunk_texts.page_vid = page_chunks.page_vid where type = 5 and page_chunks.page_vid = (select max(p1.page_vid) from page_chunks p1 where p1.chunk_id = chunk_texts.id and p1.type = page_chunks.type)");
		$db->query(NULL, "update chunk_timestamps inner join page_chunks on chunk_id = id set chunk_timestamps.page_vid = page_chunks.page_vid where type = 6 and page_chunks.page_vid = (select max(p1.page_vid) from page_chunks p1 where p1.chunk_id = chunk_timestamps.id and p1.type = page_chunks.type)");

		$db->query(NULL, "delete c1.* from chunk_texts c1 inner join chunk_texts c2 on c1.page_vid = c2.page_vid and c1.slotname = c2.slotname and c1.id != c2.id");

		$db->query(NULL, "alter table chunk_assets add foreign key (page_vid) references page_versions (id) on delete cascade on update cascade, add unique index (slotname, page_vid)");
		$db->query(NULL, "alter table chunk_features add foreign key (page_vid) references page_versions (id) on delete cascade on update cascade, add unique index (slotname, page_vid)");
		$db->query(NULL, "alter table chunk_linksets add foreign key (page_vid) references page_versions (id) on delete cascade on update cascade, add unique index (slotname, page_vid)");
		$db->query(NULL, "alter table chunk_slideshows add foreign key (page_vid) references page_versions (id) on delete cascade on update cascade, add unique index (slotname, page_vid)");
		$db->query(NULL, "alter table chunk_texts add foreign key (page_vid) references page_versions (id) on delete cascade on update cascade, add unique index (slotname, page_vid)");
		$db->query(NULL, "alter table chunk_timestamps add foreign key (page_vid) references page_versions (id) on delete cascade on update cascade, add unique index (slotname, page_vid)");

		$db->query(NULL, "drop table page_chunks");

		$db->query(NULL, "SET FOREIGN_KEY_CHECKS=1;");
	}

	public function down(Kohana_Database $db)
	{
	}
}