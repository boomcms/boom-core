<?php defined('SYSPATH') or die('No direct script access.');

class Migration_Boom_20130122105300 extends Minion_Migration_Base
{
	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "update roles set description = 'Asset manager - view and edit' where name = 'manage_assets'");
		$db->query(NULL, "update roles set description = 'People manager - view and edit' where name = 'manage_people'");
		$db->query(NULL, "delete from roles where name = 'manage_groups'");
		$db->query(NULL, "update roles set description = 'Reports - view' where name = 'view_reports'");
		$db->query(NULL, "update roles set description = 'Template manager - view and edit' where name = 'manage_templates'");
		$db->query(NULL, "update roles set description = 'Edit page - content', name = 'p_edit_page_content' where name = 'edit_page_content'");
		$db->query(NULL, "update roles set description = 'Edit page - feature image', name = 'p_edit_feature_image' where name = 'edit_feature_image'");
		$db->query(NULL, "update roles set description = 'Edit page - template', name = 'p_edit_page_template' where name = 'edit_page_template'");
		$db->query(NULL, "update roles set description = 'Edit page - URLs', name = 'p_edit_page_urls' where name = 'edit_page_urls'");
		$db->query(NULL, "update roles set description = 'Edit settings - admin', name = 'p_edit_page_admin' where name = 'edit_page_admin'");
		$db->query(NULL, "update roles set description = 'Edit settings - children (advanced)', name = 'p_edit_page_children_advamced' where name = 'edit_page_children_advanced'");
		$db->query(NULL, "update roles set description = 'Edit settings - children (basic)', name = 'p_edit_page_children_basic' where name = 'edit_page_children_basic'");
		$db->query(NULL, "update roles set description = 'Edit settings - navigation (advanced)', name = 'p_edit_page_navigation_advanced' where name = 'edit_page_nagivation_advanced'");
		$db->query(NULL, "update roles set description = 'Edit settings - navigation (basic)', name = 'p_edit_page_navigation_basic' where name = 'edit_page_navigation_basic'");
		$db->query(NULL, "update roles set description = 'Edit settings - search (advanced)', name = 'p_edit_page_search_advanced' where name = 'edit_page_search_advanced'");
		$db->query(NULL, "update roles set description = 'Edit settings - search (basic)', name = 'p_edit_page_search_basic' where name = 'edit_page_search_basic'");
		$db->query(NULL, "update roles set description = 'Page - edit', name = 'p_edit_page' where name = 'edit_page'");
		$db->query(NULL, "update roles set description = 'Page - delete', name = 'p_delete_page' where name = 'delete_page'");
		$db->query(NULL, "update roles set description = 'Page - add', name = 'p_add_page' where name = 'add_page'");
		$db->query(NULL, "update roles set description = 'Page - publish', name = 'p_publish_page' where name = 'publish_page'");
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