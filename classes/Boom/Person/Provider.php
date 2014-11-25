<?php

namespace Boom\Person;

use Cartalyst\Sentry\Users\ProviderInterface;
use Cartalyst\Sentry\Groups\GroupInterface;

class Provider implements ProviderInterface
{
    public function create(array $credentials)
    {

    }

    public function findAll()
    {
        
    }

    public function findAllInGroup(GroupInterface $group)
    {

    }

    public function findAllWithAccess($permissions)
    {
        
    }

    public function findAllWithAnyAccess(array $permissions)
    {

    }

    public function findByActivationCode($code)
    {
        
    }

    public function findByCredentials(array $credentials)
    {

    }

    public function findById($id)
    {
        return new Person(new \Model_Person($id));
    }
    
    public function findByEmail($email)
    {
        return new Person(new \Model_Person(['email' => $email]));
    }

    public function findByLogin($login)
    {
        return $this->findByEmail($login);
    }

    public function findByResetPasswordCode($code)
    {
        
    }

    /**
     *
     * @return \Boom\Person\Guest
     */
    public function getEmptyUser()
    {
        return new Guest;
    }
}
