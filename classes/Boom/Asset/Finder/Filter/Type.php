<?php

namespace Boom\Asset\Finder\Filter;

class Type extends \Boom\Finder\Filter
{
    protected $_type;

    public function __construct($types = null)
    {
        $types = is_array($types) ?: array($types);
        $this->_type = $this->removeInvalidTypes($types);
    }

    public function execute(\ORM $query)
    {
        return $query->where('asset.type', 'in', $this->_type);
    }

    private function removeInvalidTypes($types)
    {
        $validTypes = array();

        foreach ($types as $type) {
            if ($type) {
                if ( ! is_int($type) && ! ctype_digit($type)) {
                    $validTypes[] = constant('\Boom\Asset\Type::' . strtoupper($type));
                } else {
                    $validTypes[] = $type;
                }
            }
        }

        return $validTypes;
    }

    public function shouldBeApplied()
    {
        return ! empty($this->_type);
    }
}
