<?php

namespace BoomCMS\Commands;

use BoomCMS\Core\Auth;
use BoomCMS\Core\Auth\RandomPassword;
use BoomCMS\Core\Group;
use BoomCMS\Core\Person;
use BoomCMS\Events\AccountCreated;
use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Support\Facades\Event;

class CreatePerson extends Command implements SelfHandling
{
    /**
     * @var Auth\Auth
     */
    protected $auth;

    /**
     * @var array
     */
    protected $credentials;

    /**
     * @var array
     */
    protected $groups;

    /**
     * @var Group\Provider
     */
    protected $groupProvider;

    /**
     * @var Person\Provider
     */
    protected $personProvider;

    /**
     * @return void
     */
    public function __construct(array $credentials, array $groups, Auth\Auth $auth, Person\Provider $personProvider, Group\Provider $groupProvider)
    {
        $this->auth = $auth;
        $this->groupProvider = $groupProvider;
        $this->personProvider = $personProvider;

        $this->credentials = $credentials;
        $this->groups = $groups;
    }

    /**
     * @return void
     */
    public function handle()
    {
        $password = (string) new RandomPassword();
        $this->credentials['password'] = $this->auth->hash($password);

        try {
            $person = $this->personProvider->create($this->credentials);
        } catch (Person\DuplicateEmailException $e) {
        }

        if (isset($person)) {
            foreach ($this->groups as $groupId) {
                $person->addGroup($this->groupProvider->findById($groupId));
            }

            Event::fire(new AccountCreated($person, $password, $this->auth->getPerson()));

            return $person;
        } else {
            return $this->personProvider->findByEmail($this->credentials['email']);
        }
    }
}
