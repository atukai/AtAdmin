<?php

namespace AtAdmin\DataGrid\Column;

use AtAdmin\DataGrid\Column\Decorator;

class Date extends \AtAdmin\DataGrid\Column
{
    public function init()
    {
    	parent::init();
    	
        $this->setFormElement(new \Zend\Form\Element\DateTime($this->getName()))
             ->addDecorator(new Decorator\DateFormat('d.m.Y'));
    }
}