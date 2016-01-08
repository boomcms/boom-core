<?php

namespace BoomCMS\Jobs;

use BoomCMS\Auth\Hasher;
use BoomCMS\Auth\RandomPassword;
use BoomCMS\Events\AccountCreated;
use BoomCMS\Exceptions\DuplicateEmailException;
use BoomCMS\Support\Facades\Group;
use BoomCMS\Support\Facades\Person;
use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;

class CreatePerson extends Command implements SelfHandling
{
    /**
     * @var array
     */
    protected $credentials;

    /**
     * @return void
     */
    public function __construct(array $credentials)
    {
        $this->credentials = $credentials;
    }

    /**
     * @return void
     */
    public function handle()
    {
        $password = (string) new RandomPassword();
        $hasher = new Hasher();
        $this->credentials['password'] = $hasher->make($password);

        try {
            $person = Person::create($this->credentials);
        } catch (DuplicateEmailException $e) {
        }

        if (isset($person)) {
            Event::fire(new AccountCreated($person, $password, Auth::user()));

            return $person;
        } else {
            return Person::findByEmail($this->credentials['email']);
        }
    }
}
