<?php defined('SYSPATH') OR die('No direct script access.');

class Sledge_Controller_Cms_Profile extends Sledge_Controller
{
	public function action_view()
	{
		$this->template = View::factory('sledge/people/profile', array(
			'person'	=>	$this->actual_person,
		));

		// If the logged in person can manage people then add a list of all users in the CMS to the view for user mimicking
		if ($this->auth->is_mimicking() OR $this->auth->logged_in('manage_people'))
		{
			$people = ORM::factory('Person')
				->where('id', '!=', $this->person->id)
				->find_all();

			$this->template->set(array(
				'people'		=>	$people,
				'actual_person'	=>	$this->actual_person,
			));
		}
	}

	public function action_save()
	{
		// Set the person's name and theme from the POST data.
		// Set the values on $this->actual_person to avoid accidentally updating the wrong person when user mimickig.
		$this->actual_person
			->values(array(
				'name'	=>	$this->request->post('name'),
				'theme'	=>	$this->request->post('theme'),
			))
			->save();

		// If the person can view the site as another user then set user mimicking.
		if ($this->request->post('switch_user') AND ($this->auth->is_mimicking() OR $this->auth->logged_in('manage_people')))
		{
			$this->auth->mimick_user(ORM::factory('Person', $this->request->post('switch_user')));
		}
	}
}