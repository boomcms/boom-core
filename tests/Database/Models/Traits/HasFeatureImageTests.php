<?php

namespace BoomCMS\Tests\Database\Models\Traits;

use BoomCMS\Database\Models\Asset;
use Illuminate\Database\Eloquent\Builder;

trait HasFeatureImageTests
{
    public function testFeatureImageIsZeroByDefault()
    {
        $model = new $this->model();

        $this->AssertEquals(0, $model->getFeatureImageId());
    }

    public function testGetFeatureImageId()
    {
        $model = new $this->model();
        $model->{$model->getFeatureImageColumnName()} = 1;

        $this->assertEquals(1, $model->getFeatureImageId());

        $model = new $this->model();
        $this->assertEquals(0, $model->getFeatureImageId());
    }

    public function testGetFeatureImage()
    {
        $builder = m::mock(Builder::class);
        $builder->shouldReceive('first')->once();

        $model = m::mock($this->model.'[belongsTo]');
        $model
            ->shouldReceive('belongsTo')
            ->once()
            ->with(Asset::class, $model->getFeatureImageColumnName())
            ->andReturn($builder);

        $model->getFeatureImage();
    }

    public function testHasFeatureImage()
    {
        $model = new $this->model();
        $model->{$model->getFeatureImageColumnName()} = 1;

        $this->assertTrue($model->hasFeatureImage());

        $model = new $this->model();
        $this->assertFalse($model->hasFeatureImage());
    }
}
