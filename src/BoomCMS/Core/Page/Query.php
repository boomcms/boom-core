<?php

namespace BoomCMS\Core\Page;

use ReflectionClass;

class Query
{
    protected $filterAliases = [
        'parentId' => 'ParentId',
        'parent' => 'ParentPage',
        'tag' => 'Tag',
        'template' => 'Template',
        'uri' => 'uri',
    ];

    /**
     *
     * @var array
     */
    protected $params;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    public function addFilters($finder, array $params)
    {
        foreach ($params as $param => $args) {
            if (isset($this->filterAliases[$param])) {
                $class = 'BoomCMS\Core\Page\Finder\\' . $this->filterAliases[$param];

                if (is_array($args)) {
                    $reflect  = new ReflectionClass($class);
                    $filter = $reflect->newInstanceArgs($args);
                } else {
                    $filter = new $class($args);
                }

                $finder->addFilter($filter);
            }
        }

        if (isset($params['order'])) {
            list($column, $direction) = explode(' ', $params['order']);

            if ($column && $direction) {
                $column = constant('BoomCMS\Core\Page\Finder\Finder::' . strtoupper($column));
                $direction = constant('BoomCMS\Core\Page\Finder\Finder::' . strtoupper($direction));

                $finder->setOrderBy($column, $direction);
            }
        }

        if (isset($params['limit'])) {
            $finder->setLimit($params['limit']);
        }

        if (isset($params['offset'])) {
            $finder->setOffset($params['offset']);
        }

        return $finder;
    }

    public function getPages()
    {
        $finder = $this->addFilters(new Finder\Finder(), $this->params);

        return $finder->findAll();
    }
}