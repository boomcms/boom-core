<?php

use BoomCMS\Core\Auth;
use Auth\RandomPassword;
use BoomCMS\Core\Group;
use BoomCMS\Core\Person;

use Illuminate\Console\Command;
Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Support\Facades\Mail;

class CreatePerson extends Command implements SelfHandling
{
    /**
     *
     * @var Auth\Auth
     */
    protected $auth;

    /**
     *
     * @var string
     */
    protected $email;

    /**
     *
     * @var array
     */
    protected $groups;

    /**
     *
     * @var Group\Provider
     */
    protected $groupProvider;

    /**
     *
     * @var string
     */
    protected $name;

    /**
     *
     * @var Person\Provider
     */
    protected $personProvider;

    /**
     *
     * @return void
     */
    public function __construct($name, $email, array $groups, Auth\Auth $auth, Person\Provider $personProvider, Group\Provider $groupProvider)
    {
        $this->auth = $auth;
        $this->email = $email;
        $this->groups = $groups;
        $this->groupProvider = $groupProvider;
        $this->name = $name;
        $this->personProvider = $personProvider;
    }

    /**
     *
     * @return void
     */
    public function handle()
    {
        $password = (string) new RandomPassword();

        $person = $this->personProvider
            ->create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->auth->hash($password)
            ]);

        foreach ($this->groups as $groupId) {
            $person->addGroup($this->groupProvider->findById($groupId));
        }

        if (isset($password)) {
            Mail::send('boom::email.newperson', [
                'person' => $person,
                'password' => $password,
            ], function($message) use($person) {
                $message
                    ->to($person->getEmail(), $person->getName())
                    ->subject('Welcome to BoomCMS');
            });
        }

        return $person;
    }
}