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
        $this->query = new Model();
    }

    public function find()
    {
        $model = parent::find();

        return Asset::factory($model->toArray());
    }

    public function findAll()
    {
        $models = parent::findAll();
        $assets = [];

        foreach ($models as $m) {
            $assets[] = Asset::factory($m->toArray());
        }

        return $assets;
    }

    public function setOrderBy($field, $direction = null)
    {
        in_array($field, $this->_allowedOrderByColumns) || $field = 'title';

        return parent::setOrderBy($field, $direction);
    }
}
