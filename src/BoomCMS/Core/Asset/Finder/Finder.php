<?php

namespace BoomCMS\Core\Asset\Finder;

use BoomCMS\Core\Asset\Asset;
use BoomCMS\Database\Models\Asset as Model;
use BoomCMS\Foundation\Finder\Finder as BaseFinder;

class Finder extends BaseFinder
{
    /**
     * @var array
     */
    protected $allowedOrderByColumns = ['last_modified', 'title', 'downloads', 'filesize', 'uploaded_time'];

    protected $orderByAliases = [
        'last_modified' => 'version.edited_at',
        'filesize'      => 'version.filesize',
    ];

    public function __construct()
    {
        $this->query = (new Model())->withLatestVersion();
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
        in_array($field, $this->allowedOrderByColumns) || $field = 'title';

        if (isset($this->orderByAliases[$field])) {
            $field = $this->orderByAliases[$field];
        }

        return parent::setOrderBy($field, $direction);
    }
}
