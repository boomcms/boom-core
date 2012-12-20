<?php defined('SYSPATH') or die('No direct script access.');

class Migration_Sledge_20121205111000 extends Minion_Migration_Base
{

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "delete from roles where name in (
			'clone_page',
			'edit_children_visible_in_leftnav',
			'edit_children_visible_in_leftnav_cms',
			'edit_child_ordering_policy',
			'edit_default_child_template_id',
			'edit_default_grandchild_template_id',
			'edit_description',
			'edit_hidden_from_search_results',
			'edit_indexed',
			'edit_internal_name',
			'edit_keywords',
			'edit_pagetype_parent_id',
			'edit_page_links',
			'edit_parent',
			'edit_slot_bodycopy',
			'edit_slot_standfirst',
			'edit_slot_title',
			'edit_tags',
			'edit_visible_in_leftnav',
			'edit_visible_in_leftnav_cms',
			'login',
			'view_children_visible_in_leftnav',
			'view_children_visible_in_leftnav_cms',
			'view_child_ordering_policy',
			'view_default_child_template_id',
			'view_default_grandchild_template_id',
			'view_description',
			'view_feature_image',
			'view_hidden_from_search_results',
			'view_indexed',
			'view_internal_name',
			'view_keywords',
			'view_parent',
			'view_tags',
			'view_template',
			'view_visible_in_leftnav',
			'view_visible_in_leftnav_cms',
			'*')");

		$db->query(NULL, "update roles set name = 'edit_page_template' where name = 'edit_template'");

		$db->query(NULL, "insert into roles (name, description) values
			('edit_page_navigation_basic', 'Edit basic page navigation settings'),
			('edit_page_navigation_advanced', 'Edit advanced page navigation settings'),
			('edit_page_search_basic', 'Edit basic page SEO settings'),
			('edit_page_search_advanced', 'Edit advanced page SEO settings'),
			('edit_page_children_basic', 'Edit basic page children settings'),
			('edit_page_children_advanced', 'Edit advanced page children settings'),
			('edit_page_admin', 'Edit page admin settings'),
			('edit_page_content', 'Edit the content of a page')");
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