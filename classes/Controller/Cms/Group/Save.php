<?php

class Controller_Cms_Group_Save extends Controller_Cms_Group
{
    public function before()
    {
        parent::before();

        $this->_csrf_check();
    }

    public function action_add()
    {
        $this->group
            ->setName($this->request->post('name'))
            ->save();

        $this->log("Created group: ".$this->group->getName());
        $this->response->body($this->group->getId());
    }

    public function action_add_role()
    {
        $this->log("Edited the roles of group ".$this->group->getName());
        $this->group->addRole($this->request->post('role_id'), $this->request->post('allowed'), (int) $this->request->post('page_id'));
    }

    public function action_delete()
    {
        $this->log("Deleted group ".$this->group->getName());
        $this->group->delete();
    }

    public function action_remove_role()
    {
        $this->log("Edited the roles of group ".$this->group->getName());
        $this->group->removeRole($this->request->post('role_id'));
    }

    public function action_save()
    {
        $this->log("Edited group " . $this->group->getName() . " (ID: " . $this->group->getId() . ")");

        $this->group
            ->setName($this->request->post('name'))
            ->save();
    }
}
