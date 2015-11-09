<?php

namespace BoomCMS\Jobs;

use BoomCMS\Core\Auth\RandomPassword;
use BoomCMS\Core\Person\DuplicateEmailException;
use BoomCMS\Events\AccountCreated;
use BoomCMS\Support\Facades\Auth;
use BoomCMS\Support\Facades\Group;
use BoomCMS\Support\Facades\Person;
use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Support\Facades\Event;

class CreatePerson extends Command implements SelfHandling
{
    /**
     * @var array
     */
    protected $credentials;

    /**
     * @var array
     */
    protected $groups;

    /**
     * @return void
     */
    public function __construct(array $credentials, array $groups)
    {
        $this->credentials = $credentials;
        $this->groups = $groups;
    }

    /**
     * @return void
     */
    public function handle()
    {
        $password = (string) new RandomPassword();
        $this->credentials['password'] = Auth::hash($password);

        try {
            $person = Person::create($this->credentials);
        } catch (DuplicateEmailException $e) {
        }

        if (isset($person)) {
            foreach ($this->groups as $groupId) {
                $person->addGroup(Group::findById($groupId));
            }

            Event::fire(new AccountCreated($person, $password, Auth::getPerson()));

            return $person;
        } else {
            return Person::findByEmail($this->credentials['email']);
        }
    }
}
