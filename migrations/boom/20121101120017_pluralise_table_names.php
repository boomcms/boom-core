<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Initial sledge core structure.
 */
class Migration_Sledge_20121101120017 extends Minion_Migration_Base
{

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "alter table activitylog rename to logs");
		$db->query(NULL, "alter table logs change activity description varchar(255)");
		$db->query(NULL, "alter table asset rename to assets");
		$db->query(NULL, "alter table asset_v rename to asset_versions");
		$db->query(NULL, "alter table chunk rename to chunks");
		$db->query(NULL, "alter table chunk_asset rename to chunk_assets");
		$db->query(NULL, "alter table chunk_feature rename to chunk_features");
		$db->query(NULL, "alter table chunk_linkset rename to chunk_linksets");
		$db->query(NULL, "alter table chunk_slideshow rename to chunk_slideshows");
		$db->query(NULL, "alter table chunk_text rename to chunk_text");
		$db->query(NULL, "alter table group_roles rename to group_roles");
		$db->query(NULL, "alter table group_v rename to group_versions");
		$db->query(NULL, "alter table linksetlinks rename to chunk_linkset_links");
		$db->query(NULL, "alter table page rename to pages");
		$db->query(NULL, "alter table page_chunk rename to page_chunks");
		$db->query(NULL, "alter table page_uri rename to page_uris");
		$db->query(NULL, "alter table page_v rename to page_versions");
		$db->query(NULL, "alter table person_group rename to people_groups");
		$db->query(NULL, "alter table person_page rename to people_pages");
		$db->query(NULL, "alter table person_roles rename to people_roles");
		$db->query(NULL, "alter table tag rename to tags");
		$db->query(NULL, "alter table tagged_objects rename to tags_applied");
		$db->query(NULL, "alter table template rename to templates");
		$db->query(NULL, "alter table chunk_text rename to chunk_texts");
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(NULL, "alter table logs rename to activitylog");
		$db->query(NULL, "alter table activitylog change description activity varchar(50)");
		$db->query(NULL, "alter table assets rename to asset");
		$db->query(NULL, "alter table asset_versions rename to asset_v");
		$db->query(NULL, "alter table chunks rename to chunk");
		$db->query(NULL, "alter table chunk_assets rename to chunk_asset");
		$db->query(NULL, "alter table chunk_features rename to chunk_feature");
		$db->query(NULL, "alter table chunk_linksets rename to chunk_linkset");
		$db->query(NULL, "alter table chunk_slideshows rename to chunk_slideshow");
		$db->query(NULL, "alter table chunk_text rename to chunk_text");
		$db->query(NULL, "alter table group_versions rename to group_v");
		$db->query(NULL, "alter table chunk_linkset_links rename to linksetlinks");
		$db->query(NULL, "alter table pages rename to page");
		$db->query(NULL, "alter table page_chunks rename to page_chunk");
		$db->query(NULL, "alter table page_uris rename to page_uri");
		$db->query(NULL, "alter table page_versions rename to page_v");
		$db->query(NULL, "alter table people_groups rename to person_group");
		$db->query(NULL, "alter table people_pages rename to person_page");
		$db->query(NULL, "alter table people_roles rename to person_roles");
		$db->query(NULL, "alter table tags rename to tag");
		$db->query(NULL, "alter table tags_applied rename to tagged_objects");
		$db->query(NULL, "alter table templates rename to template");
		$db->query(NULL, "alter table chunk_texts rename to chunk_text");
	}
}