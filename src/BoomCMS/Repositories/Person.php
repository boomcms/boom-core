<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\Person as PersonInterface;
use BoomCMS\Contracts\Repositories\Person as PersonRepositoryInterface;
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

    /**
     * @param array $ids
     *
     * @return $this
     */
    public function deleteByIds(array $ids)
    {
        $this->model->destroy($ids);

        return $this;
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

    public function findByEmail($email)
    {
        return $this->findBy(Model::ATTR_EMAIL, $email);
    }

    public function findByGroupId($groupId)
    {
        return $this->model
            ->join('group_person', 'people.id', '=', 'person_id')
            ->where('group_id', '=', $groupId)
            ->orderBy('name', 'asc')
            ->get();
    }

    public function save(PersonInterface $person)
    {
        $person->save();

        return $person;
    }
}
