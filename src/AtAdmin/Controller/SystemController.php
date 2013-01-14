<?php

namespace AtAdmin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use BjyModulus\Module;

class SystemController extends AbstractActionController
{
    public function modulesAction()
    {
        $modulesService = $this->getServiceLocator()->get('bjymodulus_modules_service');
        $modules = Module::getLoadedModules();

        $moduleVersions = array();
        foreach ($modules as $name => $module) {
            $moduleVersions[$name] = $modulesService->getModuleCommitHashes($name);
        }

        return array('modules' => $moduleVersions);
    }
}
