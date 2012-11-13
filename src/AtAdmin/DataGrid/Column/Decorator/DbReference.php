<?php

class ATF_DataGrid_Column_Decorator_DbReference extends ATF_DataGrid_Column_Decorator_Abstract
{
    /**
     * @var null
     */
    protected $table = null;

    /**
     * @var string
     */
    protected $referenceField = '';

    /**
     * @var string
     */
    protected $resultFieldName = '';

    /**
     * @param ATF_Db_Table_Abstract $table
     * @param $referenceField
     * @param $resultFieldName
     */
    public function __construct(ATF_Db_Table_Abstract $table, $referenceField, $resultFieldName)
    {
        $this->table           = $table;
        $this->referenceField  = $referenceField;
        $this->resultFieldName = $resultFieldName;
    }

    /**
     * @param $value
     * @param $row
     * @return
     */
    public function render($value, $row)
    {
        if (!$value) {
            return '';
        }
        
        $select = $this->table->select()
                                  ->from($this->table->getName(), array($this->resultFieldName))
                                  ->where($this->referenceField . ' = ?', $value);

        return $this->table->getAdapter()->fetchOne($select);
    }
}