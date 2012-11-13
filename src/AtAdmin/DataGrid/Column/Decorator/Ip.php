<?php

class ATF_DataGrid_Column_Decorator_Ip extends ATF_DataGrid_Column_Decorator_Abstract
{
    /**
     * @param  $value
     * @param  $row
     * @return string
     */
    public function render($value, $row)
    {
        if ($value) {
            return long2ip($value);
        }
    }
}