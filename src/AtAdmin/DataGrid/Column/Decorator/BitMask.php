<?php

class ATF_DataGrid_Column_Decorator_BitMask extends ATF_DataGrid_Column_Decorator_Abstract
{
    /**
     * @var array
     */
    protected $_statuses = array();

    /**
     * @param array $statuses
     */
    public function __construct($statuses = array())
    {
        if ($statuses) {
            $this->setStatuses($statuses);            
        }
    }

    /**
     * @param array $statuses
     */
    public function setStatuses($statuses = array())
    {
        $this->_statuses = $statuses;
    }
    
    /**
     * @param $value
     * @param $row
     * @return string
     */
    public function render($value, $row)
    {
        $str = '';
        foreach ($this->_statuses as $name => $status) {
            if ($this->_checkStatus($status, $value)) {
                $str .= '<div>' . $name . ': <b>да</b></div>';
            } else {
                $str .= '<div>' . $name . ': нет</div>';
            }    
        }
        
        return $str;
    }
    
    /**
     * @param $status
     * @param $value
     * @return int
     */
    protected function _checkStatus($status, $value)
    {
        return $value & $status;
    }    
}