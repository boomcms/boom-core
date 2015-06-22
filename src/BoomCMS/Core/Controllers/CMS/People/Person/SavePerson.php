<?php

namespace BoomCMS\Core\Controllers\CMS\People\Person;

use BoomCMS\Core\Commands\CreatePerson;
use Illuminate\Support\Facades\Bus;

class SavePerson extends BasePerson
{
    public function add()
    {
		Bus::dispatch(new CreatePerson(
			[
				'email' => $this->request->input('email'),
				'name' => $this->request->input('name'),
			],
			$this->request->input('groups'),
			$this->auth,
			$this->personProvider,
			$this->groupProvider
		));
    }

    public function add_group()
    {
        foreach ($this->request->input('groups') as $groupId) {
            $group = Group\Factory::byId($groupId);
            $this->editPerson->addGroup($group);
        }
    }

    public function delete()
    {
        foreach ($this->request->input('people') as $personId) {
            $person = Person\Factory::byId($personId);
            $person->delete();
        }
    }

    public function remove_group()
    {
        $group = Group\Factory::byId($this->request->input('group_id'));
        $this->editPerson->removeGroup($group);
    }

    public function save()
    {
        $this->editPerson
			->setName($this->request->input('name'))
			->setEnabled($this->request->input('enabled') == 1);
		
		if ($superuser = $this->request->input('superuser') 
			&& $this->auth->getPerson()->isSuperuser()
			&& $this->auth->getPerson()->getId() != $this->editPerson->getId()
		) {
			$this->editPerson->setSuperuser($superuser == 1);
		}
		
		$this->personProvider->save($this->editPerson);
    }
}
