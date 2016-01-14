<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\Person as PersonInterface;
use BoomCMS\Contracts\Repositories\Person as PersonRepositoryInterface;
use BoomCMS\Database\Models\Person as Model;
use BoomCMS\Exceptions\DuplicateEmailException;
use BoomCMS\Support\Facades\Router;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Str;

class Person implements PersonRepositoryInterface, UserProvider
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

    /**
     * Retrieve a user by the given credentials.
     *
     * @param array $credentials
     *
     * @return null|Authenticatable
     */
    public function retrieveByCredentials(array $credentials)
    {
        $this->model->whereSite(Router::getActiveSite());

        foreach ($credentials as $key => $value) {
            if (!Str::contains($key, 'password')) {
                $this->model->where($key, '=', $value);
            }
        }

        return $this->model->first();
    }

    /**
     * @param mixed $id
     *
     * @return null|Authenticatable
     */
    public function retrieveById($id)
    {
        return $this->find($id);
    }

    /**
     * @param mixed  $identifier
     * @param string $token
     *
     * @return null|Authenticatable
     */
    public function retrieveByToken($identifier, $token)
    {
        $site = Router::getActiveSite();

        return $this->model
            ->whereSite($site)
            ->where($this->model->getKeyName(), '=', $identifier)
            ->where($this->model->getRememberTokenName(), '=', $token)
            ->first();
    }

    public function save(PersonInterface $person)
    {
        $person->save();

        return $person;
    }

    /**
     * @param Authenticatable $person
     * @param string          $token
     */
    public function updateRememberToken(Authenticatable $person, $token)
    {
        $person->setRememberToken($token);

        $this->save($person);
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param Authenticatable $person
     * @param array           $credentials
     *
     * @return bool
     */
    public function validateCredentials(Authenticatable $person, array $credentials)
    {
        return $this->hasher->check($credentials['password'], $person->getAuthPassword());
    }
}
