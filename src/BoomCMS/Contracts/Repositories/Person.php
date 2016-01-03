<?php

namespace BoomCMS\Contracts\Repositories;

use BoomCMS\Contracts\Models\Person as PersonInterface;

interface Person
{
    /**
     * @param array $credentials
     *
     * @return PersonInterface
     */
    public function create(array $credentials);

    /**
     * @param array $ids
     *
     * @return $this
     */
    public function deleteByIds(array $ids);

    public function findAll();

    /**
     * @param int $id
     *
     * @return PersonInterface
     */
    public function find($id);

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
     * @param PersonInterface $person
     *
     * @return $this
     */
    public function save(PersonInterface $person);
}
