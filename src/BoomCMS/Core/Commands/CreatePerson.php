<?php

namespace BoomCMS\Core\Commands;

use BoomCMS\Core\Auth;
use BoomCMS\Core\Auth\RandomPassword;
use BoomCMS\Core\Group;
use BoomCMS\Core\Person;
use BoomCMS\Core\Settings\Settings;

use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\SelfHandling;
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

        try {
            $person = $this->personProvider
                ->create([
                    'name' => $this->name,
                    'email' => $this->email,
                    'password' => $this->auth->hash($password)
                ]);
        } catch (Person\DuplicateEmailException $e) {}

        if (isset($person)) {
            foreach ($this->groups as $groupId) {
                $person->addGroup($this->groupProvider->findById($groupId));
            }

            if (isset($password)) {
                Mail::send('boom::email.newperson', [
                        'person' => $person,
                        'siteName' => Settings::get('site.name'),
                        'password' => $password,
                    ], function($message) use($person) {
                    $message
                        ->to($person->getEmail(), $person->getName())
                        ->from(Settings::get('site.admin.email'), Settings::get('site.name'))
                        ->subject('Welcome to BoomCMS');
                });
            }

            return $person;
        } else {
            return $this->personProvider->findByEmail($this->email);
        }
    }
}