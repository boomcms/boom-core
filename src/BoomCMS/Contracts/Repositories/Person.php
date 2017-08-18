<?php

namespace BoomCMS\Contracts\Repositories;

use BoomCMS\Contracts\Models\Person as PersonInterface;
use Illuminate\Support\Collection;

interface Person extends Repository
{
    /**
     * @param array $credentials
     *
     * @return PersonInterface
     */
    public function create(array $credentials);

    public function findAll();

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
}
