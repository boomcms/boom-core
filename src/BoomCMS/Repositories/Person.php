<?php

namespace BoomCMS\Repositories;

use BoomCMS\Auth\Hasher;
use BoomCMS\Contracts\Models\Person as PersonInterface;
use BoomCMS\Contracts\Models\Site as SiteInterface;
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
     * @var Hasher
     */
    protected $hasher;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->hasher = new Hasher();
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
     * @param PersonInterface $person
     *
     * @return $this
     */
    public function delete(PersonInterface $person)
    {
        $person->delete();

        return $this;
    }

    /**
     * Returns the person with the given ID.
     *
     * @param int $personId
     *
     * @return null|PersonInterface
     */
    public function find($personId)
    {
        return $this->model->find($personId);
    }

    public function findAll()
    {
        return $this->model->all();
    }

    public function findBySite(SiteInterface $site)
    {
        return $this->model
            ->with('groups')
            ->with('sites')
            ->whereSite($site)
            ->get();
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
        $query = $this->model->whereSite(Router::getActiveSite());

        foreach ($credentials as $key => $value) {
            if (!Str::contains($key, 'password')) {
                $query->where($key, '=', $value);
            }
        }

        return $query->first();
    }

    /**
     * @param int $personId
     *
     * @return null|Authenticatable
     */
    public function retrieveById($personId)
    {
        return $this->find($personId);
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
