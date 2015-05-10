<?php

namespace BoomCMS\Core\Controllers\People\Person;

use BoomCMS\Core\Auth\PasswordGenerator\PasswordGenerator;
use BoomCMS\Core\Group;
use BoomCMS\Core\Person;

class SavePerson extends BasePerson
{
    public function add(Person\Provider $provider, Group\Provider $groupProvider)
    {
        $password = PasswordGenerator::factory()->get_password();
        $encPassword = $this->auth->hash($password);

        $person = $provider
            ->create([
                'name' => $this->request->input('name'),
                'email' => $this->request->input('email'),
                'password' => $encPassword
            ])
            ->addGroup($groupProvider->findById($this->request->input('group_id')));

        if (isset($password)) {
            $email = new BoomCMS\Core\Email\Newuser($person, $password, $this->request);
            $email->send();
        }
    }

    public function add_group()
    {
        foreach ($this->request->input('groups') as $groupId) {
            $group = Group\Factory::byId($groupId);

            $this->log("Added person {$this->person->getEmail()} to group with ID {$group->getId()}");
            $this->edit_person->addGroup($group);
        }
    }

    public function delete()
    {
        foreach ($this->request->input('people') as $personId) {
            $person = Person\Factory::byId($personId);

            $this->log("Deleted person with email address: " . $person->getEmail());
            $person->delete();
        }
    }

    public function remove_group()
    {
        $group = Group\Factory::byId($this->request->input('group_id'));

        $this->log("Edited the groups for person ".$this->edit_person->getEmail());
        $this->edit_person->removeGroup($group);
    }

    public function save()
    {
        $this->log("Edited user $this->edit_person->email (ID: $this->edit_person->id) to the CMS");

        $this->edit_person
            ->values($this->request->input(), ['name', 'enabled'])
            ->update();
    }
}
