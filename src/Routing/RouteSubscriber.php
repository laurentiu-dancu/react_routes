<?php

namespace Drupal\react_routes\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Drupal\react_routes\Controller\RouteController;
use Symfony\Component\Routing\Route;
use Drupal\react_routes\Entity\Route as RouteConfig;
use Symfony\Component\Routing\RouteCollection;

class RouteSubscriber extends RouteSubscriberBase
{

    /**
     * {@inheritdoc}
     */
    protected function alterRoutes(RouteCollection $collection)
    {
        foreach ($collection as $route) {
            var_dump($route);
            die;
        }
        $routes = RouteConfig::loadMultiple();

        /* @var $route_config RouteConfig */
        foreach ($routes as $key => $route_config) {
            $options = $route_config->getOptions();
            $route = new Route($route_config->getPath(), [
                '_controller' => RouteController::class . '::renderView',
                'viewId' => $options['view'],
                'displayId' => $options['display'],
            ], [
                '_access' => 'TRUE',
            ]);
            $collection->add('react_routes.route.' . $key, $route);
        }
    }
}
