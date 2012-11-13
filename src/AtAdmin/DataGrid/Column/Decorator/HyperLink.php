<?php

class ATF_DataGrid_Column_Decorator_HyperLink extends ATF_DataGrid_Column_Decorator_Abstract
{
    /**
     * @var string
     */
    protected $url = '#';
    
    /**
     * @var array
     */
    protected $params = array();
    
    /**
     * 
     */
    public function render($value, $row)
    {
        $params = array();
        
        foreach ($this->params as $key => $param) {
                $params[$key] = $param instanceof ATF_DataGrid_Column
                              ? $row[$param->getName()]
                              : $params[$key] = $param;
        }
        
        $url = vsprintf($this->url, $params);
        
        return '<a href="' . $url . '">' . $value . '</a>';
    }

    /**
     * Set url to display hyperlink
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }
    
    /**
     * Set params for url
     */
    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }    
}