<?php

namespace AtAdmin\DataGrid\Filter;

class Equal extends AbstractFilter
{
    /**
     * @param $select
     * @param $column
     * @param mixed $value
     * @return mixed|void
     */
    public function apply($select, $column, $value)
    {
        $value = $this->applyValueType($value);

        if (strlen($value) > 0) {
        	//$columnName = $this->_findTableColumnName($select, $column->getName());
            $select->where(array($column->getName() => $value));
        }
    }
}