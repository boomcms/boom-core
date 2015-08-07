<?php

namespace BoomCMS\Core\Page;

class ChildOrderingPolicy
{
    const MANUAL = 1;
    const ALPHABETIC = 2;
    const DATE = 4;

    const ASC = 8;
    const DESC = 16;

    protected $column;
    protected $direction;
    protected $int;

    public function __construct()
    {
        $numArgs = func_num_args();

        if ($numArgs === 1) {
            $this->int = func_get_arg(0);

            $this->setFromInt($this->int);
        } elseif ($numArgs == 2) {
            $this->column = func_get_arg(0);
            $this->direction = func_get_arg(1);

            $this->setFromColumnAndDirection($this->column, $this->direction);
        }
    }

    public function asInt()
    {
        return (int) $this->int;
    }

    public function columnToInt($column)
    {
        switch ($column) {
            case ($column == 'manual' || $column == 'sequence'):
                return static::MANUAL;

            case ($column == 'date' || $column == 'visible_from'):
                return static::DATE;

            default:
                return static::ALPHABETIC;
        }
    }

    public function directionToInt($direction)
    {
        return ($direction === 'asc') ? static::ASC : static::DESC;
    }

    /**
     * Returns the name of a method which can be called on a Page object corresponding to the order column.
     */
    public function getAccessor()
    {
        $column = $this->getColumn();

        switch ($column) {
            case ($column == 'manual' || $column == 'sequence'):
                return 'getManualOrderPosition';

            case ($column == 'date' || $column == 'visible_from'):
                return 'getVisibleFromTimestamp';

            default:
                return 'getTitle';
        }
    }

    public function getColumn()
    {
        return $this->column;
    }

    public function getDirection()
    {
        return $this->direction;
    }

    protected function setFromInt($int)
    {
        if ($int & static::ALPHABETIC) {
            $this->column = 'title';
        } elseif ($int & static::DATE) {
            $this->column = 'visible_from';
        } else {
            $this->column = 'sequence';
        }

        $this->direction = ($int & static::ASC) ? 'asc' : 'desc';
    }

    protected function setFromColumnAndDirection($column, $direction)
    {
        $this->int = $this->columnToInt($column) | $this->directionToInt($direction);
    }
}
