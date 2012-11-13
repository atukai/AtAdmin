<?php

namespace AtAdmin\DataGrid\Filter;

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
            
            // @todo Add param for like template
            $spec = function (\Zend\Db\Sql\Where $where) use ($columnName,$value) {
                $where->like($columnName, '%' . $value . '%');
            };

            $select->where($spec);

            //var_dump($select->getSqlString());exit;
        }
    }
}