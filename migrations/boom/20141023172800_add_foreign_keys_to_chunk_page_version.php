<?php

class Migration_Boom_20141023172800 extends Minion_Migration_Base
{

	public function up(Kohana_Database $db)
	{
		foreach (array('texts', 'features', 'assets', 'linksets', 'slideshows', 'timestamps') as $t) {
			$table = "chunk_$t";
			$db->query(null, "delete $table.* from $table left join page_versions on $table.page_vid = page_versions.id where page_versions.id is null");
			$db->query(null, "alter table $table add foreign key (page_vid) references page_versions(id) on delete cascade on update cascade");
		}
	}

	public function down(Kohana_Database $db)
	{
	}
}