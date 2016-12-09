<?php

namespace BoomCMS\Core\Asset\Finder;

use BoomCMS\Contracts\Models\Person;
use BoomCMS\Database\Models\Asset;
use BoomCMS\Foundation\Finder\Filter as BaseFilter;
use BoomCMS\Support\Facades\Person as PersonFacade;
use Illuminate\Database\Eloquent\Builder;

class UploadedBy extends BaseFilter
{
    /**
     * @var Person
     */
    protected $person;

    public function __construct($person = null)
    {
        $this->person = $person;

        if ($this->person !== null && (is_int($this->person) || ctype_digit($this->person))) {
            $this->person = PersonFacade::find($this->person);
        }
    }

    public function build(Builder $query)
    {
        return $query->where(Asset::ATTR_CREATED_BY, $this->person->getId());
    }

    public function shouldBeApplied()
    {
        return $this->person instanceof Person;
    }
}
