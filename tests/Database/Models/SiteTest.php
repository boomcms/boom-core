<?php

namespace BoomCMS\Tests\Database\Models;

use BoomCMS\Contracts\Models\Site as SiteContract;
use BoomCMS\Database\Models\Site;

class SiteTest extends AbstractModelTestCase
{
    protected $model = Site::class;

    public function testShouldImplementSiteContract()
    {
        $this->assertTrue(in_array(SiteContract::class, class_implements(Site::class)));
    }

    public function testColumnAttributes()
    {
        $values = [
            'ATTR_ID'          => 'id',
            'ATTR_NAME'        => 'name',
            'ATTR_HOSTNAME'    => 'hostname',
            'ATTR_ADMIN_EMAIL' => 'admin_email',
            'ATTR_ANALYTICS'   => 'analytics',
            'ATTR_DEFAULT'     => 'default',
        ];

        foreach ($values as $const => $col) {
            $this->assertEquals($col, constant(Site::class."::$const"));
        }
    }

    public function testGetSetName()
    {
        $site = new Site([Site::ATTR_NAME => 'test']);
        $this->assertEquals('test', $site->getName());
    }

    public function testGetHostname()
    {
        $site = new Site([Site::ATTR_HOSTNAME => 'test']);
        $this->assertEquals('test', $site->getHostname());
    }

    public function testGetAdminEmail()
    {
        $site = new Site([Site::ATTR_ADMIN_EMAIL => 'test@test.com']);
        $this->assertEquals('test@test.com', $site->getAdminEmail());
    }

    public function testGetAnalytics()
    {
        $site = new Site([Site::ATTR_ANALYTICS => 'test']);
        $this->assertEquals('test', $site->getAnalytics());
    }

    public function testIsDefault()
    {
        $site = new Site([Site::ATTR_DEFAULT => true]);
        $this->assertEquals(true, $site->isDefault());
    }

    public function testSetDefault()
    {
        $site = new Site();

        $this->assertEquals($site, $site->setDefault(true));
        $this->assertEquals(true, $site->isDefault());
    }

    public function testSetHostname()
    {
        $site = new Site();
        $site->setHostname('test');

        $this->assertEquals('test', $site->getHostname());
    }

    public function setSetHostNameCleansValue()
    {
        $values = [
            ' test '  => 'test',
            '<b>test' => 'test',
        ];

        $site = new Site();

        foreach ($values as $in => $out) {
            $site->setHostname($in);

            $this->assertEquals($out, $site->getHostname());
        }
    }

    public function testSetName()
    {
        $site = new Site();
        $site->setName('test');

        $this->assertEquals('test', $site->getName());
    }

    public function setSetNameCleansValue()
    {
        $values = [
            ' test '  => 'test',
            '<b>test' => 'test',
        ];

        $site = new Site();

        foreach ($values as $in => $out) {
            $site->setName($in);

            $this->assertEquals($out, $site->getName());
        }
    }

    public function testSetAdminEmail()
    {
        $site = new Site();
        $site->setAdminEmail('test');

        $this->assertEquals('test', $site->getAdminEmail());
    }

    public function setSetAdminEmailCleansValue()
    {
        $values = [
            ' test '  => 'test',
            '<b>test' => 'test',
            'TEST'    => 'test',
        ];

        $site = new Site();

        foreach ($values as $in => $out) {
            $site->setAdminEmail($in);

            $this->assertEquals($out, $site->getAdminEmail());
        }
    }

    public function testSetAnalytics()
    {
        $site = new Site();
        $site->setAnalytics('test');

        $this->assertEquals('test', $site->getAnalytics());
    }
}
