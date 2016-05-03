<?php

namespace BoomCMS\Tests\Http\Controllers\People;

use BoomCMS\Database\Models\Person;
use BoomCMS\Database\Models\Site;
use BoomCMS\Http\Controllers\People\PersonSite as Controller;
use BoomCMS\Tests\Http\Controllers\BaseControllerTest;
use Mockery as m;

class PersonSiteTest extends BaseControllerTest
{
    /**
     * @var string
     */
    protected $className = Controller::class;

    public function testUpdate()
    {
        $site = new Site();

        $person = m::mock(Person::class);
        $person->shouldReceive('addSite')->once()->with($site);

        $this->controller->update($person, $site);
    }

    public function testDestroy()
    {
        $site = new Site();
        $person = m::mock(Person::class);

        $person->shouldReceive('removeSite')->with($site);

        $this->controller->destroy($person, $site);
    }
}
