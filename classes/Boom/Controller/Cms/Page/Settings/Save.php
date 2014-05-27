<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package	BoomCMS
 * @category	Controllers
 */
class Boom_Controller_Cms_Page_Settings_Save extends Controller_Cms_Page_Settings
{
	public function before()
	{
		parent::before();

		$this->_csrf_check();
	}

	public function action_admin()
	{
		parent::action_admin();

		$this->log("Saved admin settings for page " . $this->page->getTitle() . " (ID: " . $this->page->getId() . ")");

		$internal_name = $this->request->post('internal_name')? $this->request->post('internal_name') : null;
		
		$this->page
			->set('internal_name', $internal_name)
			->update();
	}

	public function action_children()
	{
		parent::action_children();

		$post = $this->request->post();

		$this->log("Saved child page settings for page ".$this->page->getTitle()." (ID: ".$this->page->getId().")");

		$expected = array('children_template_id');

		if ($this->allow_advanced)
		{
			$expected = array_merge($expected, array(
				'children_url_prefix',
				'children_visible_in_nav',
				'children_visible_in_nav_cms',
				'grandchild_template_id'
			));

			$cascade_expected = array('visible_in_nav', 'visible_in_nav_cms');
		}

		if (isset($post['children_ordering_policy']) && isset($post['children_ordering_direction']))
		{
			$this->page->setChildOrderingPolicy($post['children_ordering_policy'], $post['children_ordering_direction']);
		}

		$this->page
			->values($post, $expected)
			->update();

		if (isset($post['cascade']) && ! empty($post['cascade']))
		{
			$cascade = array();
			foreach ($post['cascade'] as $c)
			{
			$cascade[$c] = ($c == 'visible_in_nav' || $c == 'visible_in_nav_cms')?  $this->page->{"children_$c"} : $this->page->$c;
			}

			$this->page->cascade_to_children($cascade);
		}

		if (isset($post['cascade_template']))
		{
			$this->page->set_template_of_children($this->page->children_template_id);
		}
	}

	public function action_navigation()
	{
		parent::action_navigation();

		$post = $this->request->post();

		if ($this->allow_advanced)
		{
			// Reparenting the page?
			// Check that the ID of the parent has been changed and the page hasn't been set to be a child of itself.
			if ($post['parent_id'] && $post['parent_id'] != $this->page->mptt->parent_id && $post['parent_id'] != $this->page->getId())
			{
				// Check that the new parent ID is a valid page.
				$parent = \Boom\Page\Factory::byId($post['parent_id']);

				if ($parent->loaded())
				{
					$this->page
						->mptt
						->move_to_last_child($post['parent_id']);
				}
			}
		}

		$this->log("Saved navigation settings for page " . $this->page->getTitle() . " (ID: " . $this->page->getId() . ")");

		$this->page
			->values($post, array('visible_in_nav', 'visible_in_nav_cms'))
			->update();
	}

	public function action_search()
	{
		parent::action_search();

		$this->log("Saved search settings for page " . $this->page->getTitle() . " (ID: " . $this->page->getId() . ")");

		$expected = array('description', 'keywords');

		if ($this->allow_advanced)
		{
			$expected = array_merge($expected, array('external_indexing', 'internal_indexing'));
		}

		$this->page
			->values($this->request->post(), $expected)
			->update();
	}

	public function action_sort_children()
	{
		parent::action_children();

		Database::instance()->begin();
		$this->page->update_child_sequences($this->request->post('sequences'));
		Database::instance()->commit();
	}

	public function action_visibility()
	{
		parent::action_visibility();

		$post = $this->request->post();

		$this->log("Updated visibility settings for page " . $this->page->getTitle() . " (ID: " . $this->page->getId() . ")");

		$this->page->set('visible', $this->request->post('visible'));

		if ($this->page->visible)
		{
			$this->page
				->values(array(
					'visible_from'	=>	strtotime($this->request->post('visible_from')),
					'visible_to'	=>	$this->request->post('toggle_visible_to') == 1? strtotime($this->request->post('visible_to')) : null,
				));
		}

		$this->page->update();
		$this->response->body( (int) $this->page->isVisible());
	}
}