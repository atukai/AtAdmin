<?php

namespace AtAdmin\DataGrid\Column;

use AtAdmin\DataGrid\Column\Decorator;

class Literal extends \AtAdmin\DataGrid\Column
{
    /**
     * 
     */
	public function init()
	{
		parent::init();
		
		$this->addDecorator(new Decorator\Literal())
             ->setFormElement(new \Zend\Form\Element\Text($this->getName()));
	}
}