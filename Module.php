<?php

namespace AtAdmin;

use Zend\ModuleManager\ModuleManager;
use Zend\ModuleManager\ModuleEvent;

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

    /**
     * @param \Zend\EventManager\EventInterface $e
     */
    public function onBootstrap(\Zend\EventManager\EventInterface $e)
    {
        $application = $e->getApplication();
        $sm = $application->getServiceManager();
        $mm = $sm->get('ModuleManager');

        $enabledModules = $mm->getModules();

        if (in_array('BjyModulus', $enabledModules)) {
            /** @var \Zend\Mvc\Router\Http\TreeRouteStack $router  */
            $router = $application->getMvcEvent()->getRouter();
            $adminNavigation = $sm->get('admin_navigation');

            $systemMenuItem = $adminNavigation->findOneById('system-page');

            if ($systemMenuItem) {
                /** @todo How to dinamically add route? */

                $systemMenuItem->addPage(
                    array(
                        'label'       => 'Modules',
                        'route'       => 'zfcadmin/system/modules',
                        'router'      => $router,
                    )
                );
            }
        }
    }
}