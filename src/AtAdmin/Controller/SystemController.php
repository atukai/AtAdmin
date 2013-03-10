<?php

namespace AtAdmin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use BjyModulus\Module;

class SystemController extends AbstractActionController
{
    public function modulesAction()
    {
        $modulesService = $this->getServiceLocator()->get('bjymodulus_modules_service');
        $loadedModules = Module::getLoadedModules();

        $modules = array();
        foreach ($loadedModules as $name => $module) {
            $modules[$name] = $modulesService->getModuleInfo($name);
        }

        return array('modules' => $modules);
    }
}
