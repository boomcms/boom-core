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

    /**
     *
     * @param array $values
     * @return \Boom\Person\Person
     */
    public function findBy(array $values)
    {
        return new Person(new \Model_Person($values));
    }

    public function findByActivationCode($code)
    {

    }

    public function findByCredentials(array $credentials)
    {
        if ( ! isset($credentials['email'])) {
            throw new \InvalidArgumentException("Email address was not provided");
        }

        if ( ! isset($credentials['password'])) {
            throw new \InvalidArgumentException("Email address was not provided");
        }

        return $this->findBy($credentials);
    }

    public function findById($id)
    {
        return $this->findBy(['id' => $id]);
    }

    public function findByEmail($email)
    {
        return $this->findBy(['email' => $email]);
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
