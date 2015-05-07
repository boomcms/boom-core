<?php

namespace BoomCMS\Core\Asset\Finder;

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
        $asset = parent::find();

        return Factory::fromModel($asset);
    }

    public function findAll()
    {
        $assets = parent::findAll();

        return new Finder\Result($assets);
    }

    public function setOrderBy($field, $direction = null)
    {
        in_array($field, $this->_allowedOrderByColumns) || $field = 'title';

        return parent::setOrderBy($field, $direction);
    }
}
