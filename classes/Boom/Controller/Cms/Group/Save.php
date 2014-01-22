<?php

class Boom_Controller_Cms_Group_Save extends Controller_Cms_Group
{
	public function action_add()
	{
		$this->group
			->set('name', $this->request->post('name'))
			->create();

		$this->log("Created group: ".$this->group->name);
		$this->response->body($this->group->id);
	}

	public function action_add_role()
	{
		$this->log("Edited the roles of group ".$this->group->name);
		$this->group->add_role($this->request->post('role_id'), $this->request->post('allowed'), (int) $this->request->post('page_id'));
	}

	public function action_delete()
	{
		$this->log("Deleted group ".$this->group->name);
		$this->group->delete();
	}

	public function action_remove_role()
	{
		$this->log("Edited the roles of group ".$this->group->name);
		$this->group->remove_role($this->request->post('role_id'));
	}

	public function action_save()
	{
		$this->log("Edited group " . $this->group->name . " (ID: " . $this->group->id . ")");

		$this->group
			->set('name', $this->request->post('name'))
			->update();
	}
}