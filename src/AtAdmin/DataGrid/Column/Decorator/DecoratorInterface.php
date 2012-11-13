<?php

namespace AtAdmin\DataGrid\Column\Decorator;

interface DecoratorInterface
{
    public function render($value, $row);       
}