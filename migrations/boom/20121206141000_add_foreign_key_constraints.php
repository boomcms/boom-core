<?php defined('SYSPATH') or die('No direct script access.');

class Migration_Boom_20121206141000 extends Minion_Migration_Base
{

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "alter table logs add foreign key logs(person_id) references people(id) on delete cascade on update cascade");
		$db->query(NULL, "alter table assets add foreign key (uploaded_by) references people(id) on delete cascade on update cascade");
		$db->query(NULL, "alter table chunk_assets add foreign key (asset_id) references assets(id) on delete cascade on update cascade");
		$db->query(NULL, "alter table chunk_features add foreign key (target_page_id) references pages(id) on delete cascade on update cascade");
		$db->query(NULL, "alter table chunk_slideshow_slides add foreign key (asset_id) references assets(id) on delete cascade on update cascade");
		$db->query(NULL, "alter table chunk_slideshow_slides add foreign key (chunk_id) references chunk_slideshows(id) on delete cascade on update cascade");
		$db->query(NULL, "alter table chunk_linkset_links add foreign key (chunk_linkset_id) references chunk_linkset(id) on delete cascade on update cascade");
		$db->query(NULL, "alter table group_roles add foreign key (group_id) references groups(id) on delete cascade on update cascade");
		$db->query(NULL, "alter table group_roles add foreign key (role_id) references roles(id) on delete cascade on update cascade");
		$db->query(NULL, "alter table chunk_slideshow_slides add foreign key (chunk_id) references chunk_slideshows(id) on delete cascade on update cascade");
		$db->query(NULL, "alter table page_chunks add foreign key (page_vid) references page_versions(id) on delete cascade on update cascade");
		$db->query(NULL, "alter table page_links add foreign key (page_id) references pages(id) on delete cascade on update cascade");
		$db->query(NULL, "alter table page_versions add foreign key (page_id) references pages(id) on delete cascade on update cascade");
		$db->query(NULL, "alter table page_versions add foreign key (edited_by) references people(id) on delete cascade on update cascade");
		$db->query(NULL, "alter table people_pages add foreign key (person_id) references people(id) on delete cascade on update cascade");
		$db->query(NULL, "alter table people_pages add foreign key (page_id) references pages(id) on delete cascade on update cascade");
		$db->query(NULL, "alter table people_roles add foreign key (role_id) references roles(id) on delete cascade on update cascade");
		$db->query(NULL, "alter table people_roles add foreign key (group_id) references groups(id) on delete cascade on update cascade");
		$db->query(NULL, "alter table people_roles add foreign key (person_id) references people(id) on delete cascade on update cascade");
		$db->query(NULL, "alter table tags_applied add foreign key (tag_id) references tags(id) on delete cascade on update cascade");
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function down(Kohana_Database $db)
	{
	}
}