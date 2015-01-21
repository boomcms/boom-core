<?php

namespace Boom\Group;

use Cartalyst\Sentry\Groups\ProviderInterface;

class Provider implements ProviderInterface
{
    public function create(array $attributes)
    {
        
    }

    public function findAll()
    {

    }

    public function findById($id)
    {
        return new Group(new \Model_Group($id));
    }

    public function findByName($name)
    {

    }

}
