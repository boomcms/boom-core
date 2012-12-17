<?php defined('SYSPATH') OR die('No direct script access.');

/**
*
* @package Sledge
* @category Controllers
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2012, Hoop Associates
*/
class Sledge_Controller_Plugin_Search extends Kohana_Controller
{
	public function action_index()
	{
		$q = $this->request->post('search') or $q = $this->request->query('search');

		if ($q)
		{
			$p = $this->request->post('page') or $p = $this->request->query('page') or $p = 1;

			if ( ! class_exists('SphinxClient'))
			{
				require "sphinxapi.php";
			}

			// Create an instance of the API.
			$sx = new SphinxClient;
			$sx->setLimits( (int) ($p - 1) * 20, 20);
			$sx->setGroupBy('page_id', SPH_GROUPBY_ATTR, "@relevance desc");

			$index_name = Kohana::$config->load('instance')->db_name . "-page_text-";
			$index_name .= ($this->editor->state() === Editor::EDIT)? 'cms' : 'site';

			$results = $sx->query($q, "$index_name, $index_name-delta");

			$pages = array();
			foreach ( (array) @$results['matches'] as $result)
			{
				$pages[] = new Model_Page($result['attrs']['page_id']);
			}

			// Setup the main template.
			$v = View::factory('sledge/plugin/search/results');
			$v->pages = $pages;
			$v->results = $returned = count(@$results['matches']);
			$v->returned = $returned;
			$v->total = $total = $results['total_found'];
			$v->query = $q;

			// Pagination template.
			if ($total > $returned)
			{
				$count = ceil($total / 20);
				$pagination = View::factory('pagination/query');
				$pagination->total_pages = $count;
				$pagination->current_page = $p;
				$pagination->base_url = Request::initial()->link();
				$pagination->previous_page = $p - 1;
				$pagination->next_page = ($p == $count)? 0 : ($p + 1);
				$v->pagination = $pagination;
			}

			$this->response->body($v);
		}

	}

}