<?php

use Boom\Auth\PasswordGenerator\PasswordGenerator;
use Boom\Group;
use Boom\Person;

class Controller_Cms_Person_Save extends Controller_Cms_Person
{
    public function before()
    {
        parent::before();

        $this->_csrf_check();
    }

    public function action_add()
    {
        $password = PasswordGenerator::factory()->get_password();
        $encPassword = $this->auth->hash($password);

        $this->edit_person
            ->setName($this->request->post('name'))
            ->setEmail($this->request->post('email'))
            ->setEncryptedPassword($encPassword)
            ->save()
            ->addGroup(Group\Factory::byId($this->request->post('group_id')));

        if (isset($password)) {
            $email = new Boom\Email\Newuser($this->edit_person, $password, $this->request);
            $email->send();
        }
    }

    public function action_add_group()
    {
        $groups = $this->request->post('groups');

        foreach ($groups as $group_id) {
            $this->log("Added person $this->person->email to group with ID $group_id");
            $this->edit_person->add_group($group_id);
        }
    }

    public function action_delete()
    {
        foreach ($this->request->post('people') as $personId) {
            $person = Person\Factory::byId($personId);

            $this->log("Deleted person with email address: " . $person->getEmail());
            $person->delete();
        }
    }

    public function action_remove_group()
    {
        $this->log("Edited the groups for person ".$this->edit_person->email);
        $this->edit_person->remove_group($this->request->post('group_id'));
    }

    public function action_save()
    {
        $this->log("Edited user $this->edit_person->email (ID: $this->edit_person->id) to the CMS");

        $this->edit_person
            ->values($this->request->post(), array('name', 'enabled'))
            ->update();
    }
}
