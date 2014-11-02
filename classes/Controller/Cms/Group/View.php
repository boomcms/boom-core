<?php

class Controller_Cms_Group_View extends Controller_Cms_Group
{
    public function action_add()
    {
        $this->template = View::factory("$this->viewDirectory/add", array(
            'group' => new Model_Group(),
        ));
    }

    public function action_edit()
    {
        $this->template = View::factory("$this->viewDirectory/edit", array(
            'group'        =>    $this->group,
            'general_roles'    =>    ORM::factory('Role')
                ->where('name', 'not like', 'p_%')
                ->order_by('description', 'asc')
                ->find_all(),
            'page_roles'    =>    ORM::factory('Role')
                ->where('name', 'like', 'p_%')
                ->order_by('description', 'asc')
                ->find_all(),
        ));
    }

    public function action_list_roles()
    {
        $roles = $this->group->getRoles( (int) $this->request->query('page_id'));
        $roles = json_encode($roles);

        $this->response
            ->headers('Content-Type', static::JSON_RESPONSE_MIME)
            ->body($roles);
    }
}
