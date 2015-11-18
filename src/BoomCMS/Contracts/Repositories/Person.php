<?php

namespace BoomCMS\Contracts\Repositories;

use BoomCMS\Core\Auth\Guest;
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
     * @param string $token
     *
     * @return PersonInterface
     */
    public function findByAutoLoginToken($token);

    /**
     * @param int $id
     *
     * @return PersonInterface
     */
    public function find($id);

    /**
     * @param string $email
     *
     * @return PersonInterface
     */
    public function findByEmail($email);

    /**
     * @return Guest
     */
    public function getEmptyUser();

    /**
     * @param PersonInterface $person
     *
     * @return $this
     */
    public function save(PersonInterface $person);
}
