<?php

class Controller_Cms_Group_Save extends Controller_Cms_Group
{
    public function action_add()
    {
        $this->group
            ->setName($this->request->input('name'))
            ->save();

        $this->log("Created group: ".$this->group->getName());
        $this->response->body($this->group->getId());
    }

    public function action_add_role()
    {
        $this->log("Edited the roles of group ".$this->group->getName());
        $this->group->addRole($this->request->input('role_id'), $this->request->input('allowed'), (int) $this->request->input('page_id'));
    }

    public function action_delete()
    {
        $this->log("Deleted group ".$this->group->getName());
        $this->group->delete();
    }

    public function action_remove_role()
    {
        $this->log("Edited the roles of group ".$this->group->getName());
        $this->group->removeRole($this->request->input('role_id'));
    }

    public function action_save()
    {
        $this->log("Edited group " . $this->group->getName() . " (ID: " . $this->group->getId() . ")");

        $this->group
            ->setName($this->request->input('name'))
            ->save();
    }
}
