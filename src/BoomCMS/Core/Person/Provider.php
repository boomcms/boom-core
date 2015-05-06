<?php

namespace BoomCMS\Core\Person;

use Cartalyst\Sentry\Users\ProviderInterface;
use Cartalyst\Sentry\Groups\GroupInterface;

use BoomCMS\Core\Models\Person as Model;

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

    public function findAndCache(Model $model)
    {
        if ($model->id) {
            $this->cache[$model->id] = $model;
        }

        return new Person( (array) $model);
    }

    /**
     *
     * @return \Boom\Person\Person
     */
    public function findBy($key, $value)
    {
        $model = Model::where($key, '=', $value)->first();

        return $model? $this->findAndCache($model) : new Guest();
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

        $model = Model::where('email', '=', $credentials['email'])
                ->where('password', '=', $credentials['password'])
                ->first();

        return $model? $this->findAndCache($model) : new Guest();
    }

    public function findById($id)
    {
        return $this->findBy('id', $id);
    }

    public function findByEmail($email)
    {
        return $this->findBy('email', $email);
    }

    public function findByLogin($login)
    {
        return $this->findByEmail($login);
    }

    public function findByResetPasswordCode($code)
    {
        return $this->findBy('reset_password_code', $code);
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
