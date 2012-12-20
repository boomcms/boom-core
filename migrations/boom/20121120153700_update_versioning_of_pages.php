<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Initial sledge core structure.
 */
class Migration_Sledge_20121120153700 extends Minion_Migration_Base
{

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "alter table pages
			add visible_from int unsigned,
			add visible_to int unsigned,
			add internal_name varchar(64),
			add external_indexing boolean not null default true,
			add internal_indexing boolean not null default true,
			add visible_in_nav boolean not null default true,
			add visible_in_nav_cms boolean not null default true,
			add children_visible_in_nav boolean not null default true,
			add children_visible_in_nav_cms boolean not null default true,
			add children_template_id tinyint unsigned,
			add children_link_prefix varchar(2048),
			add children_ordering_policy tinyint unsigned,
			add children_prompt_for_template boolean default true,
			add grandchild_template_id tinyint unsigned,
			add keywords varchar(255),
			add description text");

		$db->query(NULL, "update pages inner join page_versions on pages.active_vid = page_versions.id set
			pages.visible_from = page_versions.visible_from,
			pages.visible_to = page_versions.visible_to,
			pages.internal_name = page_versions.internal_name,
			pages.external_indexing = page_versions.indexed,
			pages.internal_indexing = page_versions.hidden_from_search_results,
			pages.visible_in_nav = page_versions.visible_in_leftnav,
			pages.visible_in_nav_cms = page_versions.visible_in_leftnav_cms,
			pages.children_visible_in_nav = page_versions.children_visible_in_leftnav,
			pages.children_visible_in_nav_cms = page_versions.children_visible_in_leftnav_cms,
			pages.children_template_id = page_versions.default_child_template_id,
			pages.grandchild_template_id = page_versions.default_grandchild_template_id,
			pages.children_link_prefix = page_versions.default_child_link_prefix,
			pages.children_ordering_policy = page_versions.child_ordering_policy,
			pages.children_prompt_for_template = page_versions.prompt_for_child_template,
			pages.keywords = page_versions.keywords,
			pages.description = page_versions.description");

		$db->query(NULL, "alter table page_versions
			drop visible_from,
			drop visible_to,
			drop internal_name,
			drop indexed,
			drop hidden_from_search_results,
			drop visible_in_leftnav,
			drop visible_in_leftnav_cms,
			drop children_visible_in_leftnav,
			drop children_visible_in_leftnav_cms,
			drop default_child_template_id,
			drop default_grandchild_template_id,
			drop default_child_link_prefix,
			drop child_ordering_policy,
			drop prompt_for_child_template,
			drop keywords,
			drop description,
			add embargoed_until int unsigned,
			add stashed boolean default false,
			change deleted page_deleted boolean default false,
			change audit_person edited_by smallint unsigned,
			change audit_time edited_time int unsigned,
			change rid page_id smallint unsigned,
			drop hidden_from_internal_links");

		$db->query(NULL, "update page_versions set embargoed_until = unix_timestamp() where published = 1");

		$db->query(NULL, "alter table pages drop active_vid, drop published_vid");
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