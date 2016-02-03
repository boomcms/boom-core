<?php

namespace BoomCMS\Tests\Chunk;

use BoomCMS\Chunk\Location;
use BoomCMS\Database\Models\Page;
use BoomCMS\Tests\AbstractTestCase;
use Lootils\Geo\Location as GeoLocation;
use Mockery as m;

class ChunkLocationTest extends AbstractTestCase
{
    public function testTitleAndAddressAreDisabledByDefault()
    {
        $expected = [
            'data-boom-title'   => false,
            'data-boom-address' => false,
        ];
        $chunk = $this->getChunk();

        $this->assertEquals($expected, $chunk->attributes());
    }

    public function testEnablingTitleSection()
    {
        $chunk = $this->getChunk();
        $chunk->title();

        $this->assertArraySubset(['data-boom-title' => true], $chunk->attributes());
    }

    public function testEnablingAddressSection()
    {
        $chunk = $this->getChunk();
        $chunk->address();

        $this->assertArraySubset(['data-boom-address' => true], $chunk->attributes());
    }

    public function testGetLocation()
    {
        $attrs = [
            'lat' => 51.520169,
            'lng' => -0.098481,
        ];

        $chunk = $this->getChunk($attrs);
        $location = $chunk->getLocation();

        $this->assertInstanceOf(GeoLocation::class, $location);
        $this->assertEquals($attrs['lat'], $location->latitude());
        $this->assertEquals($attrs['lng'], $location->longitude());
    }

    /**
     * @param array  $attrs
     * @param string $slotname
     * @param bool   $editable
     *
     * @return Location
     */
    protected function getChunk($attrs = [], $slotname = 'test', $editable = true)
    {
        return m::mock(Location::class, [new Page([]), $attrs, $slotname, $editable])->makePartial();
    }
}
