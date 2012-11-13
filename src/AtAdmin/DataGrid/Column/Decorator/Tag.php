<?php

class ATF_DataGrid_Column_Decorator_Tag extends ATF_DataGrid_Column_Decorator_Abstract
{
    /**
     * @var
     */
    protected $tag;

    /**
     * @var string
     */
    protected $placement = self::REPLACE;

    /**
     * @param string $tag
     */
    public function __construct($tag)
    {
        $this->tag = (string) $tag;
    }

    /**
     * @param $tag
     * @return ATF_DataGrid_Column_Decorator_Tag
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
        return $this;
    }    

    /**
     * Render value wrapping into tag
     *
     * @param $value
     * @param $row
     * @return string
     */
    public function render($value, $row)
    {
        $content = '<' . $this->tag . '>' . $value . '</' . $this->tag . '>';
        return $content;
    }
}