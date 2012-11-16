<?php defined('SYSPATH') OR die('No direct script access.');
/**
*
* @package Sledge
* @category Controllers
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2012, Hoop Associates
*/
class Sledge_Controller_Plugin_Asset extends Sledge_Controller
{
	public function action_library()
	{
		// Get the parent tag from the post data.
		// This is the topmost tag that we'll display.
		// But if another tag ID is given in the URL options then we'll list the assets in that tag instead, as long as it's a child of this tag.
		$parent_tag = $this->request->post('parent_tag');
		$tag = $this->request->post('tag');

		// Only continue if a tag ID is present.
		if ($parent_tag AND $tag)
		{
			// Load the tag data from the DB.
			$parent_tag = ORM::factory('Tag', $parent_tag);

			if ($parent_tag->pk() == $tag)
			{
				$tag = $parent_tag;
			}
			else
			{
				$tag = ORM::factory('Tag', $tag);
			}

			// Check that the tag is valid.
			if ($tag->loaded())
			{
				// If the tag we're displaying isn't the parent tag then it has to be a descendent.
				//if ($tag->pk() === $parent_tag->pk() OR $parent_tag->mptt->is_in_parents($tag->mptt))
				if ($tag->pk() === $parent_tag->pk())
				{
					// Get the page number.
					$page = Arr::get($this->request->post(), 'page', Arr::get(Request::initial()->param('options'), 1, 1));

					// Number of assets to display on each page, default is 15.
					$perpage = Arr::get($this->request->post(), 'perpage', 15);

					// Get the total number of assets in this tag.
					$total = ORM::factory('Asset')->where('tag', '=', $tag)->count_all();
					$total_pages = ceil($total / $perpage);

					// Get a page of assets.
					$assets = ORM::factory('Asset')
								->where('tag', '=', $tag)
								->offset( ($page - 1) * $perpage)
								->limit($perpage)
								->order_by('title', 'asc')
								->find_all();
				
					// Get the names and IDs of child tags to we can filter the results.
					$kids = DB::select('tags.id', 'tag_v.name')
								->from('tags')
								->where('tags.parent_id', '=', $parent_tag->id)
								->execute()
								->as_array();

					// Put the information in the template.
					$v = View::factory('sledge/plugin/asset/library');
					$v->tag = $tag;
					$v->parent_tag = $parent_tag;
					$v->assets = $assets;
					$v->total = $total;
					$v->kids = $kids;
					$v->url = $url = ($this->request->post('url'))? $this->request->post('url') : Request::initial()->param('page')->link();

					if ($total_pages > 1)
					{
						$pagination = View::factory('sledge/plugin/asset/library/pagination');
						$pagination->current_page = $page;
						$pagination->total_pages = $total_pages;
						$pagination->tag = $tag;
						$pagination->url = $url;
						$v->pagination = $pagination;
					}

					$this->response->body($v);
				}
			}
		}
	}
}