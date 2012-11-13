<?php

namespace AtAdmin\DataGrid\Filter;

use Zend\Db\Sql\Predicate;

class Like extends AbstractFilter
{
    /**
     * Returns the result of applying $value
     *
     * @param  mixed $value
     * @return mixed
     */
    public function apply($select, $column, $value)
    {
        $value = $this->applyValueType($value);
        
        if (strlen($value) > 0) {
            
            //$columnName = $this->findTableColumnName($select, $column->getName());
            $columnName = $column->getName();
            
            /*var_dump($column->getName());
            die;
*/
            // @todo Вынести формирование шаблона LIKE в метод
            $select = $select->where(new Predicate\Like($columnName, '%' . $value . '%'));

            //var_dump($select->getSqlString());exit;
        }
    }
}