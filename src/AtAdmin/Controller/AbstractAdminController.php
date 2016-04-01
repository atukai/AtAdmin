<?php

namespace AtAdmin\Controller;

use Zend\Mvc\Controller\AbstractActionController;

abstract class AbstractAdminController extends AbstractActionController
{
    abstract public function listAction();
    abstract public function createAction();
    abstract public function editAction();
    abstract public function deleteAction();
}