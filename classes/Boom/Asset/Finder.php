<?php

namespace Boom\Asset;

use \Boom;
use \ORM as ORM;

class Finder extends Boom\Finder\Finder
{
    /**
	 *
	 * @var array
	 */
    protected $_allowedOrderByColumns = ['last_modified', 'title', 'downloads', 'filesize', 'uploaded_time'];

    public function __construct()
    {
        $this->_query = ORM::factory('Asset');
    }

    public function find()
    {
        $asset = parent::find();

        return Asset\Factory::fromModel($asset);
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
