<?php

namespace AtAdmin\Controller;

use Zend\Mvc\Controller\AbstractActionController;

abstract class AbstractAdminController extends AbstractActionController
{
    abstract public function getAction();
    abstract public function createAction();
    abstract public function editAction();
    abstract public function deleteAction();
}