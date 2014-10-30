<?php

namespace AtAdmin;

use AtAdmin\Form\FormManager;

class Module
{
    public function getModuleDependencies()
    {
        return array('ZfcAdmin');
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

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

    /**
     * @return array
     */
    public function getControllerConfig()
    {
        return array(
            'invokables' => array(
                'AtAdmin\Controller\Dashboard'              => 'AtAdmin\Controller\DashboardController',
                'AtAdmin\Controller\Settings'               => 'AtAdmin\Controller\SettingsController',
                'AtAdmin\Controller\AbstractCrudController' => 'AtDataGrid\Controller\AbstractCrudController'
            ),
        );
    }

    /**
     * @return array
     */
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'AtAdmin\Form\FormManager' => function ($sm) {
                    $manager = new FormManager($sm->get('Request'));
                    return $manager;
                },
            ),
        );
    }
}