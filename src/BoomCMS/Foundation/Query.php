<?php

namespace BoomCMS\Foundation;

use ReflectionClass;

class Query
{
    /**
     * @var array
     */
    protected $params;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    public function addFilters(Finder\Finder $finder, array $params)
    {
        foreach ($params as $param => $args) {
            $param = strtolower($param);

            if (isset($this->filterAliases[$param])) {
                $class = $this->filterAliases[$param];

                if (is_array($args)) {
                    $reflect = new ReflectionClass($class);

                    if ($param === 'not' || $param === 'ignorepages') {
                        $pages[] = $args;
                        $filter = $reflect->newInstanceArgs($pages);
                    } else {
                        $filter = $reflect->newInstanceArgs($args);
                    }
                } else {
                    $filter = new $class($args);
                }

                $finder->addFilter($filter);
            }
        }

        return $finder;
    }

    public function configurePagination(Finder\Finder $finder, array $params)
    {
        if (isset($params['order'])) {
            list($column, $direction) = explode(' ', $params['order']);

            if ($column && $direction) {
                $finder->setOrderBy($column, $direction);
            }
        }

        if (isset($params['limit'])) {
            $finder->setLimit($params['limit']);

            if (isset($params['page'])) {
                $finder->setOffset(($params['page'] - 1) * $params['limit']);
            }
        }

        if (isset($params['offset'])) {
            $finder->setOffset($params['offset']);
        }

        return $finder;
    }
}
