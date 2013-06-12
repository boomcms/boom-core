<?php

class Boom_Controller_Cms_People_View extends Controller_Cms_People
{
	public function action_add()
	{
		$this->template = View::factory($this->_view_directory."new", array(
			'groups'	=>	ORM::factory('Group')->names(),
		));
	}

	public function action_add_group()
	{
		$groups = ORM::factory('Group')
			->names(
				DB::Select('group_id')
					->from('people_groups')
					->where('person_id', '=', $this->edit_person->id)
			);

		// Set the response template.
		$this->template = View::factory("$this->_view_directory/addgroup", array(
			'person'	=>	$this->edit_person,
			'groups'	=>	$groups,
		));
	}

	public function action_index()
	{
		$this->template = View::factory("$this->_view_directory/index", array(
			'groups'	=>	ORM::factory('Group')->names(),
		));
	}

	public function action_list()
	{
		// Import query string paramaters
		$page	=	max(1, $this->request->query('page'));
		$group	=	$this->request->query('tag');
		$order	=	$this->request->query('order');
		$sortby		=	$this->request->query('sortby');

		$query = new Model_Person;

		if ($group)
		{
			// Restrict results by group.
			$query
				->join('people_groups', 'inner')
				->on('person_id', '=', 'id')
				->where('group_id', '=', $group);
		}

		if ( ! ($order == 'desc' OR $order == 'asc'))
		{
			// Sort ascending by default.
			$order = 'asc';
		}

		$column = 'name';

		if ( strpos( $sortby, '-' ) > 1 ){
			$sort_params = explode( '-', $sortby );
			$column = $sort_params[0];
			$order = $sort_params[1];
		}

		$query->order_by('name', $order);

		$count = clone $query;
		$total = $count->count_all();

		$people = $query
			->limit(30)
			->offset( ($page - 1) * 30)
			->find_all();

		$this->template = View::factory('boom/people/list', array(
			'people'	=>	$people,
			'group'	=>	new Model_Group($group),
			'total'	=>	$total,
			'sortby'		=>	$sortby,
			'order'	=>	$order,
		));

		$pages = ceil($total / 30);

		if ($pages > 1)
		{
			$url = '#tag/' . $this->request->query('tag');
			$pagination = View::factory('pagination/query', array(
				'current_page'	=>	$page,
				'total_pages'	=>	$pages,
				'base_url'		=>	$url,
				'previous_page'	=>	$page - 1,
				'next_page'	=>	($page == $pages)? 0 : ($page + 1),
			));

			$this->template->set('pagination', $pagination);
		}
	}

	public function action_view()
	{
		if ( ! $this->edit_person->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$this->template = View::factory($this->_view_directory."view", array(
			'person'		=>	$this->edit_person,
			'request'		=>	$this->request,
			'activities'	=>	$this->edit_person->logs->order_by('time', 'desc')->limit(50)->find_all(),
			'groups'		=>	$this->edit_person->groups->order_by('name', 'asc')->find_all(),
		));
	}
}