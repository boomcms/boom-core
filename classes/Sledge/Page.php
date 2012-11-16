<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Helper functions for working with pages.
 * @package	Sledge
 * @category	Helpers
 */
abstract class Sledge_Page
{
	/**
	 * Returns the column name which should be used for joining the page table to the page_versions table.
	 * i.e. Whether active_vid or published_vid should be used.
	 * These changes depending on whether the person can edit the page or the editor state.
	 *
	 * Requires a Model_Page and an Auth instance as its parameters to check whether the person can edit the page.
	 *
	 * @param	Model_Page	$page
	 * @param	Auth			$auth
	 * @return 	string
	 */
	public static function join_column(Model_Page $page, Auth $auth)
	{
		// If the person can't edit this page or they are previewing published pages only
		// then they can only see the published version.
		if (Editor::state() === Editor::PREVIEW_PUBLISHED OR ! $auth->logged_in('edit_page', $page))
		{
			return "published_vid";
		}

		// For anything else show the active version.
		return "active_vid";
	}
}