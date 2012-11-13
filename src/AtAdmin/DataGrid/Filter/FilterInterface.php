<?php

namespace AtAdmin\DataGrid\Filter;

/**
 *
 */
interface FilterInterface
{
    /**
     * Returns the result of filtering $value
     *
     * @param  mixed $value
     * @return mixed
     */
    public function apply(\Zend\Db\Sql\Select $select, $column, $value);
}