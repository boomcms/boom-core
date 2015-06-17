<?php

namespace BoomCMS\Core\Controllers\CMS\Group;

class Save extends BaseController
{
    public function add()
    {
		$group = $this->provider->create(['name' => $this->request->input('name')]);
		
		return $group->getId();
    }

    public function add_role()
    {
        $this->log("Edited the roles of group ".$this->group->getName());
        $this->group->addRole($this->request->input('role_id'), $this->request->input('allowed'), (int) $this->request->input('page_id'));
    }

    public function delete()
    {
        $this->log("Deleted group ".$this->group->getName());
        $this->group->delete();
    }

    public function remove_role()
    {
        $this->log("Edited the roles of group ".$this->group->getName());
        $this->group->removeRole($this->request->input('role_id'));
    }

    public function save()
    {
        $this->log("Edited group " . $this->group->getName() . " (ID: " . $this->group->getId() . ")");

        $this->group
            ->setName($this->request->input('name'))
            ->save();
    }
}
