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