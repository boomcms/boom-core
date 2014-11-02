<?php

namespace Boom;

abstract class Finder
{
    const ASC = 'asc';
    const DESC = 'desc';

    protected $_filters = array();
    protected $_filtersApplied = false;
    protected $_query;

    public function addFilter(Finder\Filter $filter)
    {
        $this->_filters[] = $filter;

        return $this;
    }

    protected function _applyFilters(\ORM $query)
    {
        $this->_filtersApplied = true;

        foreach ($this->_filters as $filter) {
            if ($filter->shouldBeApplied()) {
                $query = $filter->execute($query);
            }
        }

        return $query;
    }

    public function count()
    {
        if (! $this->_filtersApplied) {
            $this->_query = $this->_applyFilters($this->_query);
        }

        $countQuery = clone $this->_query;

        return $countQuery->count_all();
    }

    public function find()
    {
        if (! $this->_filtersApplied) {
            $this->_query = $this->_applyFilters($this->_query);
        }

        return $this->_query->find();
    }

    public function findAll()
    {
        if (! $this->_filtersApplied) {
            $this->_query = $this->_applyFilters($this->_query);
        }

        return $this->_query->find_all();
    }

    public function setLimit($limit)
    {
        $this->_query->limit($limit);

        return $this;
    }

    public function setOffset($offset)
    {
        $this->_query->offset($offset);

        return $this;
    }

    public function setOrderBy($field, $direction = null)
    {
        $this->_query->order_by($field, $direction);

        return $this;
    }
}
