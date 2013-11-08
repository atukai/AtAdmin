<?php

namespace AtAdmin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class SystemController extends AbstractActionController
{
    public function settingsAction()
    {
        return new ViewModel();
    }
}