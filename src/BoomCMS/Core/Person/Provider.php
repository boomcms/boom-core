<?php

namespace BoomCMS\Core\Person;

use BoomCMS\Core\Models\Person as Model;

class Provider
{
    public function create(array $credentials)
    {

    }

    public function findAndCache(Model $model)
    {
        if ($model->id) {
            $this->cache[$model->id] = $model;
        }

        return new Person($model->toArray());
    }

    /**
     *
     * @return \Boom\Person\Person
     */
    public function findBy($key, $value)
    {
        $model = Model::where($key, '=', $value)->first();

        return $model ? $this->findAndCache($model) : new Guest();
    }

    public function findByActivationCode($code)
    {

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
        return new Guest();
    }
}
