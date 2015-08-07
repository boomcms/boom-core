<?php

use BoomCMS\Core\Settings\Store;

class SettingsTest extends TestCase
{
    public function testSettingsLoaded()
    {
        $store = $this->getStore();
    }

    public function testGetAllSettingsWithNoneReturnsArray()
    {
        $store = $this->getStore();
        $this->assertEquals([], $store->getAllSettings());
    }

    public function testGetAllSettingsReturnsAllSettings()
    {
        $settings = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        $store = $this->getStore($settings);
        $this->assertEquals($settings, $store->getAllSettings());
    }

    public function testGetReturnsValue()
    {
        $settings = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        $store = $this->getStore($settings);
        $this->assertEquals($settings['key1'], $store->get('key1'));
    }

    public function testGetReturnsNull()
    {
        $store = $this->getStore();
        $this->assertEquals(null, $store->get('key1'));
    }

    public function testGetReturnsDefault()
    {
        $default = 'default';

        $store = $this->getStore();
        $this->assertEquals($default, $store->get('key1', $default));
    }

    public function testSetReturnsStore()
    {
        $store = $this->getStore();
        $this->assertInstanceOf('BoomCMS\Core\Settings\Store', $store->set('key1', 'value'));
    }

    public function testGetAfterSet()
    {
        $store = $this->getStore();
        $store->set('key1', 'value1');
        $this->assertEquals('value1', $store->get('key1'));
    }

    public function testSetWithArray()
    {
        $store = $this->getStore();
        $store->set([
            'key1' => 'value1',
            'key2' => 'value2',
        ]);

        $this->assertEquals('value1', $store->get('key1'));
        $this->assertEquals('value2', $store->get('key2'));
    }

    public function testSetPersists()
    {
        $settings = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        $filesystem = $this->getMock('Illuminate\Filesystem\Filesystem');
        $filesystem
            ->expects($this->once())
            ->method('exists')
            ->with($this->equalTo(storage_path().'/boomcms/settings.json'))
            ->will($this->returnValue(true));

        $filesystem
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo(storage_path().'/boomcms/settings.json'))
            ->will($this->returnValue($settings ? json_encode($settings) : null));

        $filesystem
            ->expects($this->once())
            ->method('put')
            ->with($this->equalTo(storage_path().'/boomcms/settings.json'), json_encode($settings));

        $store = new Store($filesystem);
        $store->set($settings);
    }

    public function testExistsWithValue()
    {
        $store = $this->getStore(['key1' => 'value1']);

        $this->assertTrue($store->exists('key1'));
    }

    public function testExistsWithNullValue()
    {
        $store = $this->getStore(['key1' => null]);

        $this->assertTrue($store->exists('key1'));
    }

    public function testExistsWithNoValue()
    {
        $store = $this->getStore([]);

        $this->assertFalse($store->exists('key1'));
    }

    protected function getStore(array $settings = null)
    {
        $filesystem = $this->getMock('Illuminate\Filesystem\Filesystem');
        $filesystem
            ->expects($this->once())
            ->method('exists')
            ->with($this->equalTo(storage_path().'/boomcms/settings.json'))
            ->will($this->returnValue(true));

        $filesystem
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo(storage_path().'/boomcms/settings.json'))
            ->will($this->returnValue($settings ? json_encode($settings) : null));

        return new Store($filesystem);
    }
}
