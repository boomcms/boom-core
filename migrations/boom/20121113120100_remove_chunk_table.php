<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Initial boom core structure.
 */
class Migration_Boom_20121113120100 extends Minion_Migration_Base
{

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "alter table chunk_assets add slotname varchar(50), change chunk_id id mediumint unsigned auto_increment");
		$db->query(NULL, "alter table chunk_features add slotname varchar(50), change chunk_id id mediumint unsigned auto_increment");
		$db->query(NULL, "alter table chunk_linksets add slotname varchar(50), change chunk_id id mediumint unsigned auto_increment");
		$db->query(NULL, "alter table chunk_slideshows add slotname varchar(50), change chunk_id id mediumint unsigned auto_increment");
		$db->query(NULL, "alter table chunk_texts add slotname varchar(50), change chunk_id id mediumint unsigned auto_increment");

		$db->query(NULL, "update chunk_assets inner join chunks on chunks.id = chunk_assets.id set chunk_assets.slotname = chunks.slotname");
		$db->query(NULL, "update chunk_features inner join chunks on chunks.id = chunk_features.id set chunk_features.slotname = chunks.slotname");
		$db->query(NULL, "update chunk_linksets inner join chunks on chunks.id = chunk_linksets.id set chunk_linksets.slotname = chunks.slotname");
		$db->query(NULL, "update chunk_slideshows inner join chunks on chunks.id = chunk_slideshows.id set chunk_slideshows.slotname = chunks.slotname");
		$db->query(NULL, "update chunk_texts inner join chunks on chunks.id = chunk_texts.id set chunk_texts.slotname = chunks.slotname");

		$db->query(NULL, "alter table page_chunks add type tinyint unsigned");
		$db->query(NULL, "update page_chunks inner join chunks on page_chunks.chunk_id = chunks.id set page_chunks.type = 1 where chunks.type = 'asset'");
		$db->query(NULL, "update page_chunks inner join chunks on page_chunks.chunk_id = chunks.id set page_chunks.type = 2 where chunks.type = 'feature'");
		$db->query(NULL, "update page_chunks inner join chunks on page_chunks.chunk_id = chunks.id set page_chunks.type = 3 where chunks.type = 'linkset'");
		$db->query(NULL, "update page_chunks inner join chunks on page_chunks.chunk_id = chunks.id set page_chunks.type = 4 where chunks.type = 'slideshow'");
		$db->query(NULL, "update page_chunks inner join chunks on page_chunks.chunk_id = chunks.id set page_chunks.type = 5 where chunks.type = 'text'");

		$db->query(NULL, "alter table page_chunks drop index chunk_page_page_vid_chunk_id");
		$db->query(NULL, "create index page_chunks_page_vid_chunk_id_type on page_chunks(page_vid, chunk_id, type)");
		$db->query(NULL, "drop table chunks");
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