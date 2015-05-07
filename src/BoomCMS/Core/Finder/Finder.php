<?php

namespace BoomCMS\Core\Finder;

use Illuminate\Database\Eloquent\Model;

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

    protected function applyFilters(Model $query)
    {
        $this->filtersApplied = true;

        foreach ($this->filters as $filter) {
            if ($filter->shouldBeApplied()) {
                $query = $filter->execute($query);
            }
        }

        return $query;
    }

    public function count()
    {
        if (! $this->filtersApplied) {
            $this->query = $this->applyFilters($this->query);
        }

        $countQuery = clone $this->query;

        return $countQuery->count();
    }

    public function find()
    {
        if (! $this->filtersApplied) {
            $this->query = $this->applyFilters($this->query);
        }

        return $this->query->find();
    }

    public function findAll()
    {
        if (! $this->filtersApplied) {
            $this->query = $this->applyFilters($this->query);
        }

        return $this->query->find_all();
    }

    public function setLimit($limit)
    {
        $this->query->limit($limit);

        return $this;
    }

    public function setOffset($offset)
    {
        $this->query->offset($offset);

        return $this;
    }

    public function setOrderBy($field, $direction = null)
    {
        $this->query->orderBy($field, $direction);

        return $this;
    }
}
