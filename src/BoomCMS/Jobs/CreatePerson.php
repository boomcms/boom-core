<?php

namespace BoomCMS\Jobs;

use BoomCMS\Auth\Hasher;
use BoomCMS\Auth\RandomPassword;
use BoomCMS\Events\AccountCreated;
use BoomCMS\Support\Facades\Person;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;

class CreatePerson extends Command
{
    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $name;

    /**
     * @return void
     */
    public function __construct($email, $name)
    {
        $this->name = $name;
        $this->email = $email;
    }

    /**
     * @return void
     */
    public function handle()
    {
        $password = (string) new RandomPassword();
        $hasher = new Hasher();

        $person = Person::create([
            'name'     => $this->name,
            'email'    => $this->email,
            'password' => $hasher->make($password),
        ]);

        Event::fire(new AccountCreated($person, $password, Auth::user()));

        return $person;
    }
}
