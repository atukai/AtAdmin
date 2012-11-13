<?php

namespace AtAdmin\DataGrid\Filter;

class Equal extends AbstractFilter
{
    /**
     * @param \Zend\Db\Sql\Select $select
     * @param $column
     * @param mixed $value
     * @return mixed|void
     */
    public function apply($select, $column, $value)
    {
        $value = $this->_applyValueType($value);

        if (strlen($value) > 0) {
        	//$columnName = $this->_findTableColumnName($select, $column->getName());
            $select->where($column->getName(), $value);
        }
    }
}