<?php

namespace BoomCMS\Tests\Database\Models;

use BoomCMS\Database\Models\Person;
use BoomCMS\Database\Models\Site;
use Illuminate\Database\Eloquent\Builder;
use Mockery as m;

class PersonTest extends AbstractModelTestCase
{
    protected $model = Person::class;

    public function testAddSite()
    {
        $site = new Site();
        $person = m::mock(Person::class.'[sites,attach]');

        $person->shouldReceive('sites')
            ->once()
            ->andReturnSelf();

        $person->shouldReceive('attach')
            ->once()
            ->with($site);

        $this->assertEquals($person, $person->addSite($site));
    }

    public function testAddSites()
    {
        $sites = [new Site(), new Site()];
        $person = m::mock(Person::class.'[addSite]');

        foreach ($sites as $s) {
            $person
                ->shouldReceive('addSite')
                ->once()
                ->with($s)
                ->andReturnSelf();
        }

        $this->assertEquals($person, $person->addSites($sites));
    }

    public function testGetAuthIdentifier()
    {
        $person = new Person();
        $person->id = 1;

        $this->assertEquals($person->id, $person->getAuthIdentifier());
    }

    public function testGetAuthPassword()
    {
        $person = new Person(['password' => 'test']);

        $this->assertEquals($person->password, $person->getAuthPassword());
    }

    public function testGetEmailReturnsEmailAttribute()
    {
        $email = 'test@test.com';
        $person = new Person([Person::ATTR_EMAIL => $email]);

        $this->assertEquals($email, $person->getEmail());
    }

    public function testGetRememberTokenName()
    {
        $person = new Person();

        $this->assertEquals(Person::ATTR_REMEMBER_TOKEN, $person->getRememberTokenName());
    }

    public function testGetSites()
    {
        $sites = [new Site(), new Site()];
        $person = m::mock(Person::class.'[sites,orderBy,get]');

        $person->shouldReceive('sites')
            ->once()
            ->andReturnSelf();

        $person->shouldReceive('orderBy')
            ->once()
            ->with('name', 'asc')
            ->andReturnSelf();

        $person->shouldReceive('get')
            ->once()
            ->andReturn($sites);

        $this->assertEquals($sites, $person->getSites());
    }

    public function testHasSite()
    {
        $site = new Site();
        $site->{Site::ATTR_ID} = 1;

        $query = m::mock(Site::class);
        $person = m::mock(Person::class)->makePartial();

        $person
            ->shouldReceive('sites')
            ->once()
            ->andReturn($query);

        $query
            ->shouldReceive('where')
            ->once()
            ->with(Site::ATTR_ID, '=', $site->getId())
            ->andReturnSelf();

        $query
            ->shouldReceive('exists')
            ->once()
            ->andReturn(true);

        $this->assertTrue($person->hasSite($site));
    }

    public function testIsSuperuserDefaultFalse()
    {
        $person = new Person([]);

        $this->assertFalse($person->isSuperuser());
    }

    public function testIsSuperuserReturnsTrue()
    {
        $person = new Person(['superuser' => true]);

        $this->assertTrue($person->isSuperuser());
    }

    public function testRemoveSite()
    {
        $site = new Site();
        $person = m::mock(Person::class)->makePartial();

        $person
            ->shouldReceive('sites')
            ->once()
            ->andReturnSelf();

        $person
            ->shouldReceive('detach')
            ->once()
            ->with($site);

        $this->assertEquals($person, $person->removeSite($site));
    }

    public function testScopeWhereSite()
    {
        $site = new Site();
        $site->{Site::ATTR_ID} = 1;
        $person = new Person();
        $query = m::mock(Builder::class);

        $query
            ->shouldReceive('join')
            ->once()
            ->with('person_site', 'people.id', '=', 'person_site.person_id')
            ->andReturnSelf();

        $query
            ->shouldReceive('where')
            ->once()
            ->with('person_site.site_id', '=', $site->getId())
            ->andReturnSelf();

        $this->assertEquals($query, $person->scopeWhereSite($query, $site));
    }

    public function testSetGetRememberLoginToken()
    {
        $person = new Person([]);
        $person->setRememberToken('token');

        $this->assertEquals('token', $person->getRememberToken());
    }

    public function testSetEmailSetsEmailAddress()
    {
        $email = 'test@test.com';
        $person = new Person([]);
        $person->setEmail($email);

        $this->assertEquals($email, $person->getEmail());
    }

    public function testEmailAddressIsAlwaysLowecase()
    {
        $email = 'test@test.com';
        $person = new Person([]);
        $person->setEmail(strtoupper($email));

        $this->assertEquals($email, $person->getEmail());
    }

    public function testEmailAddressIsTrimmed()
    {
        $email = 'test@test.com';
        $person = new Person([]);
        $person->setEmail(' '.$email.' ');

        $this->assertEquals($email, $person->getEmail());
    }
}
