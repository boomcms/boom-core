<?php defined('SYSPATH') or die('No direct script access.');

class Migration_Boom_20131113120000 extends Minion_Migration_Base
{

	public function up(Kohana_Database $db)
	{
		$db->query(null, "alter table tags engine = InnoDB");
		$db->query(null, "alter table tags change id id int(11) unsigned auto_increment");
		$db->query(null, "create table chunk_tags (id mediumint unsigned primary key auto_increment, slotname varchar(50) not null, tag_id int(11) unsigned not null, page_vid int(10) unsigned not null)");
		$db->query(null, "alter table chunk_tags add foreign key (tag_id) references tags(id) on update cascade on delete cascade");
		$db->query(null, "alter table chunk_tags add foreign key (page_vid) references page_versions(id) on update cascade on delete cascade");
		$db->query(null, "create unique index chunk_tags_page_vid_slotname on chunk_tags(page_vid, slotname)");

	}

	public function down(Kohana_Database $db)
	{
		$db->query(null, "drop table chunk_tags");
	}
}