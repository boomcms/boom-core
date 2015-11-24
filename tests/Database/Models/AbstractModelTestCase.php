<?php

namespace BoomCMS\Tests\Database\Models;

use BoomCMS\Tests\AbstractTestCase;

abstract class AbstractModelTestCase extends AbstractTestCase
{
    public function testIdShouldBeGuarded()
    {
        $model = new $this->model(['id' => 1]);

        $this->assertNull($model->id);
    }

    public function testGetIdReturnsIdAttribute()
    {
        $model = new $this->model();
        $model->id = 1;

        $this->assertEquals(1, $model->id);
    }
}
