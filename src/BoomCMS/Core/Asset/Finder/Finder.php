<?php

namespace BoomCMS\Core\Asset\Finder;

use BoomCMS\Database\Models\Asset as Model;
use BoomCMS\Foundation\Finder\Finder as BaseFinder;

class Finder extends BaseFinder
{
    /**
     * @var array
     */
    protected $allowedOrderByColumns = [
        'last_modified',
        'title',
        'downloads',
        'filesize',
        'created_at',
        'published_at',
    ];

    protected $orderByAliases = [
        'last_modified' => 'version.created_at',
        'filesize'      => 'version.filesize',
        'created_at'    => 'assets.created_at',
    ];

    public function __construct()
    {
        $this->query = (new Model())->withLatestVersion()
            ->with('versions')
            ->with('versions.editedBy')
            ->with('uploadedBy');
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
