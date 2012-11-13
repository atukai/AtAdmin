<?php

class ATF_DataGrid_Column_Decorator_BitMaskDict extends ATF_DataGrid_Column_Decorator_Abstract
{
    protected $_choises = array();
    protected $_delimiter = '<br/>';

    public function __construct($options = array())
    {
        if (array_key_exists('choises', $options)) {
            $this->setChoises($options['choises']);
        }
        if (array_key_exists('delimiter', $options)) {
            $this->setDelimiter($options['delimiter']);
        }
    }

    public function setChoises($choises = array())
    {
        $this->_choises = $choises;
    }

    public function setDelimiter($delimiter = '<br/>')
    {
        $this->_delimiter = $delimiter;
    }
    
    public function render($value, $row)
    {
        $rs = array();
        foreach ($this->_choises as $k => $v) {
            if (($value & $k) == $k) {
                $rs[] = $v;
            }
        }
        return implode($this->_delimiter, $rs);
    }
}