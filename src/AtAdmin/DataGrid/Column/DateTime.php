<?php

namespace AtAdmin\DataGrid\Column;

use AtAdmin\DataGrid\Column\Decorator;

class DateTime extends \AtAdmin\DataGrid\Column
{
    /**
     * Extensions
     */
    public function init()
    {
        parent::init();
        
        $this->setFormElement(new \Zend\Form\Element\DateTime($this->getName()))
             ->addDecorator(new Decorator\DateFormat());
    }
}