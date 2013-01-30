<?php defined('SYSPATH') or die('No direct script access.');

class Migration_Boom_20130130140100 extends Minion_Migration_Base
{
	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "alter table pages engine=InnoDB;");
		$db->query(NULL, "alter table page_versions engine=InnoDB;");
		$db->query(NULL, "alter table chunk_assets engine=InnoDB;");
		$db->query(NULL, "alter table chunk_text engine=InnoDB;");
		$db->query(NULL, "alter table page_chunks engine=InnoDB;");
		$db->query(NULL, "alter table chunk_features engine=InnoDB;");
		$db->query(NULL, "alter table chunk_linksets engine=InnoDB;");
		$db->query(NULL, "alter table chunk_slideshow_slides engine=InnoDB;");
		$db->query(NULL, "alter table chunk_slideshows engine=InnoDB;");
		$db->query(NULL, "alter table chunk_text_assets engine=InnoDB;");
		$db->query(NULL, "alter table chunk_linkset_links engine=InnoDB;");
		$db->query(NULL, "alter table page_urls engine=InnoDB;");
	}

	public function down(Kohana_Database $db) {}
}