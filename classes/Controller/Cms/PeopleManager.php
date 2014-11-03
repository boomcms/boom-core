<?php

use \Boom\Person\Finder as PersonFinder;
use \Boom\Group\Finder as GroupFinder;

class Controller_Cms_PeopleManager extends Controller_Cms
{
    public function before()
    {
        parent::before();

        $this->authorization('manage_people');
    }

    public function action_index()
    {
        $finder = new PersonFinder();
        $finder
            ->addFilter(new PersonFinder\Filter\GroupId($this->request->query('group')))
            ->setOrderBy('name', 'asc');

        $this->template = new View("boom/people/list", [
            'people' => $finder->findAll()
        ]);
    }

    protected function _show(View $view = null)
    {
        if ( ! $this->request->is_ajax()) {
            $finder = new GroupFinder();

            $this->template = View::factory("boom/people/manager", [
                'groups' => $finder->findAll(),
                'content' => $view,
            ]);
        }
    }

    public function after()
    {
        $this->_show($this->template);

        parent::after();
    }
}
