<?php

namespace AtAdmin;

use AtAdmin\Controller\DashboardController;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use Zend\ServiceManager\Factory\InvokableFactory;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * @return array
     */
    public function getControllerConfig()
    {
        return [
            'factories' => [
                DashboardController::class => InvokableFactory::class,
            ],
        ];
    }

    /**
     * @param MvcEvent $e
     */
    public function onBootstrap(MvcEvent $e)
    {
        $app = $e->getApplication();
        $app->getEventManager()->attach(MvcEvent::EVENT_DISPATCH, function (MvcEvent $e) use ($app) {
            $config = $app->getServiceManager()->get('config');

            $routeMatch = $e->getRouteMatch();
            $controller = $e->getTarget();
            if (!$routeMatch instanceof RouteMatch
                || 0 !== strpos($routeMatch->getMatchedRouteName(), 'at-admin')
                || $controller->getEvent()->getResult()->terminate()
            ) {
                return;
            }

            if (isset($config['at-admin']['layout'])) {
                $controller->layout($config['at-admin']['layout']);
            }
        });
    }
}