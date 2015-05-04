<?php

namespace AtAdmin;

use Zend\EventManager\EventInterface;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function getControllerConfig()
    {
        return [
            'invokables' => [
                'AtAdmin\Controller\Dashboard'              => 'AtAdmin\Controller\DashboardController',
                'AtAdmin\Controller\AbstractCrudController' => 'AtDataGrid\Controller\AbstractCrudController'
            ],
        ];
    }

    /**
     * @param MvcEvent $e
     */
    public function onBootstrap(MvcEvent $e)
    {
        $app = $e->getApplication();
        $app->getEventManager()->attach(MvcEvent::EVENT_DISPATCH, function (MvcEvent $e) {
            $app    = $e->getApplication();
            $config = $app->getServiceManager()->get('config');

            $match      = $e->getRouteMatch();
            $controller = $e->getTarget();
            if (!$match instanceof RouteMatch
                || 0 !== strpos($match->getMatchedRouteName(), 'at-admin')
                || $controller->getEvent()->getResult()->terminate()
            ) {
                return;
            }

            $layout = $config['at-admin']['admin_layout'];
            $controller->layout($layout);
        });
    }
}