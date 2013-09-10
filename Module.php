<?php

namespace AtAdmin;

use Zend\ModuleManager\ModuleManager;

class Module
{
    /**
     * @param $moduleManager
     */
    public function init(ModuleManager $moduleManager)
    {
        $moduleManager->loadModule('ZfcAdmin');
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}