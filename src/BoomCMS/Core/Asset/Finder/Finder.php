<?php

namespace BoomCMS\Core\Asset\Finder;

use BoomCMS\Core\Asset\Asset;
use BoomCMS\Core\Finder\Finder as BaseFinder;
use BoomCMS\Core\Models\Asset as Model;

class Finder extends BaseFinder
{
    /**
	 *
	 * @var array
	 */
    protected $_allowedOrderByColumns = ['last_modified', 'title', 'downloads', 'filesize', 'uploaded_time'];

    public function __construct()
    {
        $this->query = new Model()->withLatestVersion();
    }

    protected function createFrom(Model $model)
    {
        $attrs = $model->toArray();

        return Asset::factory($attrs);
    }

    public function find()
    {
        $model = parent::find();

        return $this->createFrom($model);
    }

    public function findAll()
    {
        $models = parent::findAll();
        $assets = [];

        foreach ($models as $m) {
            $assets[] = $this->createFrom($m);
        }

        return $assets;
    }

    public function setOrderBy($field, $direction = null)
    {
        in_array($field, $this->_allowedOrderByColumns) || $field = 'title';

        return parent::setOrderBy($field, $direction);
    }
}
