<?php

namespace BoomCMS\Contracts\Repositories;

use BoomCMS\Contracts\Models\Person as PersonInterface;
use Illuminate\Support\Collection;

interface Person
{
    /**
     * @param array $credentials
     *
     * @return PersonInterface
     */
    public function create(array $credentials);

    /**
     * @param PersonInterface $person
     *
     * @return $this
     */
    public function delete(PersonInterface $person);

    public function findAll();

    /**
     * @param int $personId
     *
     * @return PersonInterface
     */
    public function find($personId);

    /**
     * @param int $groupId
     *
     * @return array
     */
    public function findByGroupId($groupId);

    /**
     * @param string $email
     *
     * @return PersonInterface
     */
    public function findByEmail($email);

    /**
     * Returns the users who have uploaded assets.
     *
     * @return Collection
     */
    public function getAssetUploaders();

    /**
     * @param PersonInterface $person
     *
     * @return $this
     */
    public function save(PersonInterface $person);
}
