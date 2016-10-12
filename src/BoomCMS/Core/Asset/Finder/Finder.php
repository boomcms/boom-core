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
        'uploaded_time',
    ];

    protected $orderByAliases = [
        'last_modified' => 'version.edited_at',
        'filesize'      => 'version.filesize',
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
