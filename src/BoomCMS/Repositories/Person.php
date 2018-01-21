<?php

namespace BoomCMS\Repositories;

use BoomCMS\Auth\Hasher;
use BoomCMS\Contracts\Models\Site as SiteInterface;
use BoomCMS\Database\Models\Person as Model;
use BoomCMS\Foundation\Repository;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Person extends Repository implements UserProvider
{
    /**
     * @var Hasher
     */
    protected $hasher;

    /**
     * @var SiteInterface
     */
    protected $site;

    /**
     * @param Model $model
     */
    public function __construct(Model $model, SiteInterface $site = null)
    {
        $this->hasher = new Hasher();
        $this->model = $model;
        $this->site = $site;
    }

    public function create(array $credentials)
    {
        return $this->model->create($credentials);
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
            ->orderBy(Model::ATTR_NAME, 'asc')
            ->get();
    }

    /**
     * {@inheritdoc}
     *
     * @return Collection
     */
    public function getAssetUploaders(): Collection
    {
        return $this->model
            ->has('assets')
            ->orderBy(Model::ATTR_NAME, 'asc')
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
        $query = $this->model->whereSite($this->site);

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
        return $this->model
            ->whereSite($this->site)
            ->where($this->model->getKeyName(), '=', $identifier)
            ->where($this->model->getRememberTokenName(), '=', $token)
            ->first();
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
        // User cannot be validated if they don't have a password
        if (empty($person->getAuthPassword())) {
            return false;
        }

        return $this->hasher->check($credentials['password'], $person->getAuthPassword());
    }
}
