<?php

namespace AtAdmin\DataGrid\Column;

use AtAdmin\DataGrid\Column\Decorator;

class Textarea extends \AtAdmin\DataGrid\Column
{
    public function init()
    {
        parent::init();

        $this->setFormElement(new \Zend\Form\Element\Textarea($this->getName()));
    }
}