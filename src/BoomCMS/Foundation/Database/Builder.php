<?php

namespace BoomCMS\Foundation\Database;

use Illuminate\Database\Eloquent\Collection;
use ReflectionClass;

class Query
{
    /**
     * @var array
     */
    protected $filters = [];

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var array
     */
    protected $params;

    /**
     * @param Model $model
     * @param array $params
     */
    public function __construct(Model $model, array $params)
    {
        $this->model = $model;
        $this->params = $params;
    }

    public function buildFilters()
    {
        foreach ($this->params as $param => $args) {
            $param = strtolower($param);

            if (isset($this->filterAliases[$param])) {
                $class = $this->filterAliases[$param];

                if (is_array($args)) {
                    $reflect = new ReflectionClass($class);
                    $filter = $reflect->newInstanceArgs($args);
                } else {
                    $filter = new $class($args);
                }

                $filter->build($this->model);

                $this->filters = [];
            }
        }
    }

    public function configurePagination()
    {
        if (isset($this->params['order'])) {
            list($column, $direction) = explode(' ', $this->params['order']);

            if ($column && $direction) {
                $this->model->orderBy($column, $direction);
            }
        }

        if (isset($this->params['limit'])) {
            $this->model->take($this->params['limit']);

            if (isset($this->params['page'])) {
                $skip = ($this->params['page'] - 1) * $this->params['limit'];
                $this->model->skip($skip);
            }
        }

        if (isset($this->params['offset'])) {
            $this->model->skip($this->params['offset']);
        }
    }

    public function count(): int
    {
        $this->buildFilters();

        return $this->model->count();
    }

    public function executeFilters()
    {
        foreach ($this->filters as $filter) {
            $filter->execute($this->model);
        }
    }

    public function get(): Collection
    {
        $this->buildFilters();
        $this->executeFilters();

        return $this->model->get();
    }
}
