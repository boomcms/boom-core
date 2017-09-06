<?php

namespace BoomCMS\Support\Traits;

use BoomCMS\Contracts\Models\Person as PersonInterface;
use BoomCMS\Database\Models\Person;

trait HasCreatedBy
{
    public function createdBy()
    {
        return $this->hasOne(Person::class, Person::ATTR_ID, $this->getCreatedByColumnName());
    }

    /**
     * @return PersonInterface
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    public function getCreatedByColumnName(): string
    {
        return self::ATTR_CREATED_BY;
    }

    public function wasCreatedBy(PersonInterface $person): bool
    {
        $createdBy = $this->getCreatedBy();

        if ($createdBy === null) {
            return false;
        }

        return $createdBy->is($person);
    }
}
