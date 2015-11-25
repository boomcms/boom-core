<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\Person as PersonInterface;
use BoomCMS\Contracts\Repositories\Person as PersonRepositoryInterface;
use BoomCMS\Core\Auth\Guest;
use BoomCMS\Database\Models\Person as Model;
use BoomCMS\Exceptions\DuplicateEmailException;

class Person implements PersonRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function create(array $credentials)
    {
        $existing = $this->findByEmail($credentials['email']);

        if ($existing) {
            throw new DuplicateEmailException($credentials['email']);
        }

        return $this->model->create($credentials);
    }

    public function deleteByIds(array $ids)
    {
        $this->destroy($ids);
    }

    public function find($id)
    {
        return $this->findBy(Model::ATTR_ID, $id);
    }

    public function findAll()
    {
        return $this->model->all();
    }

    /**
     * @return Person
     */
    public function findBy($key, $value)
    {
        return $this->model->where($key, '=', $value)->first();
    }

    public function findByAutoLoginToken($token)
    {
        return $this->findBy(Model::ATTR_REMEMBER_TOKEN, $token);
    }

    public function findByEmail($email)
    {
        return $this->findBy(Model::ATTR_EMAIL, $email);
    }

    public function findByLogin($login)
    {
        return $this->findByEmail($login);
    }

    public function findByResetPasswordCode($code)
    {
        return $this->findBy(Model::ATTR_REMEMBER_TOKEN, $code);
    }

    /**
     * @return Guest
     */
    public function getEmptyUser()
    {
        return new Guest();
    }

    public function save(PersonInterface $person)
    {
        if ($person->getId()) {
            $model = isset($this->cache[$person->getId()]) ?
                $this->cache[$person->getId()]
                : Model::find($person->getId());

            $model->update($person->toArray());
        } else {
            $model = Model::create($person->toArray());
            $person->setId($model->id);
        }

        return $person;
    }
}
