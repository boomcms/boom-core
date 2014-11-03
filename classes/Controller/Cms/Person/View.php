<?php

use Boom\Group;

class Controller_Cms_Person_View extends Controller_Cms_Person
{
    public function action_add()
    {
        $this->template = View::factory($this->viewDirectory."new", array(
            'groups'    =>    ORM::factory('Group')->names(),
        ));
    }

    public function action_add_group()
    {
        $finder = new Group\Finder;
        $finder
            ->addFilter(new Group\Finder\Filter\ExcludingPersonsGroups($this->edit_person))
            ->setOrderBy('name');

        $this->template = View::factory("$this->viewDirectory/addgroup", array(
            'person' => $this->edit_person,
            'groups' => $finder->findAll(),
        ));
    }

    public function action_view()
    {
        if ( ! $this->edit_person->loaded()) {
            throw new HTTP_Exception_404();
        }

        $this->template = View::factory($this->viewDirectory."view", array(
            'person' => $this->edit_person,
            'request' => $this->request,
            'groups' => $this->edit_person->getGroups(),
            'activities' => array(),
        ));
    }
}
