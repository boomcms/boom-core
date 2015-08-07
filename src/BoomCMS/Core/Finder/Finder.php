<?php

namespace BoomCMS\Core\Finder;

use Illuminate\Database\Eloquent\Builder;

abstract class Finder
{
    const ASC = 'asc';
    const DESC = 'desc';

    protected $filters = [];
    protected $filtersApplied = false;
    protected $query;

    public function addFilter(Filter $filter)
    {
        $this->filters[] = $filter;

        return $this;
    }

    protected function applyFilters(Builder $query, $execute = false)
    {
        $this->filtersApplied = true;

        foreach ($this->filters as $filter) {
            if ($filter->shouldBeApplied()) {
                $query = $filter->build($query);

                if ($execute === true) {
                    $query = $filter->execute($query);
                }
            }
        }

        return $query;
    }

    public function count()
    {
        if (!$this->filtersApplied) {
            $this->query = $this->applyFilters($this->query);
        }

        return $this->query->count();
    }

    public function find()
    {
        if (!$this->filtersApplied) {
            $this->query = $this->applyFilters($this->query, true);
        }

        return $this->query->first();
    }

    public function findAll()
    {
        if (!$this->filtersApplied) {
            $this->query = $this->applyFilters($this->query, true);
        }

        return $this->query->get();
    }

    public function setLimit($limit)
    {
        $this->query = $this->query->take($limit);

        return $this;
    }

    public function setOffset($offset)
    {
        $this->query = $this->query->skip($offset);

        return $this;
    }

    public function setOrderBy($field, $direction = null)
    {
        $this->query = $this->query->orderBy($field, $direction);

        return $this;
    }
}
