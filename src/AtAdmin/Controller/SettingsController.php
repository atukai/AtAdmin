<?php

namespace AtAdmin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class SettingsController extends AbstractActionController
{
    public function generalAction()
    {
        return new ViewModel();
    }
}