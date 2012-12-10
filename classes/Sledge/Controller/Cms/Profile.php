<?php defined('SYSPATH') OR die('No direct script access.');

class Sledge_Controller_Cms_Profile extends Sledge_Controller
{
	public function action_view()
	{
		$real_user = $this->auth->get_real_user();

		$this->template = View::factory('sledge/people/profile', array(
			'person'	=>	$real_user,
		));

		// If the logged in person can manage people then add a list of all users in the CMS to the view for user mimicking
		if ($this->auth->is_mimicking() OR $this->auth->logged_in('manage_people'))
		{
			$people = ORM::factory('Person')
				->where('id', '!=', $this->person->id)
				->find_all();

			$this->template->set(array(
				'people'		=>	$people,
				'actual_person'	=>	$real_user,
			));
		}
	}

	public function action_save()
	{
		// Set the person's name and theme from the POST data.
		$this->person
			->values(array(
				'name'	=>	$this->request->post('name'),
				'theme'	=>	$this->request->post('theme'),
			))
			->update();

		// If the person can view the site as another user then set user mimicking.
		if ($this->auth->is_mimicking() OR $this->auth->logged_in('manage_people'))
		{
			$person = ($this->request->post('switch_user'))? ORM::factory('Person', $this->request->post('switch_user')) : NULL;

			$this->auth->mimick_user($person);
		}
	}
}