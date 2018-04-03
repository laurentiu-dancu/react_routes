<?php

namespace Drupal\react_routes\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Drupal\react_routes\Controller\RouteController;
use Symfony\Component\Routing\Route;
use Drupal\react_routes\Entity\Route as RouteConfig;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class RouteSubscriber.
 *
 * @package Drupal\react_routes\Routing
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    $routes = RouteConfig::loadMultiple();

    /* @var $route_config RouteConfig */
    foreach ($routes as $key => $route_config) {

      $options = $route_config->getOptions();
      switch ($route_config->getType()) {
        case 'view':
          $route = new Route($route_config->getPath(), [
            '_controller' => RouteController::class . '::renderView',
            'viewId' => $options['view'],
            'displayId' => $options['display'],
          ], [
            '_access' => 'TRUE',
          ]);
          $collection->add('react_routes.route.' . $key, $route);
          break;

        case 'rest':
          $route = new Route($route_config->getPath(), [
            '_controller' => RouteController::class . '::renderResource',
            '_rest_resource_config' => $options['resource_id'],
          ], [
            '_access' => 'TRUE',
          ]);
          $parameters['_rest_resource_config'] = [
            'type' => 'entity:rest_resource_config',
            'converter' => 'paramconverter.entity',
          ];
          foreach ($options['parameters'] as $parameter => $type) {
            $parameters[$parameter] = [
              'type' => $type,
              'converter' => 'paramconverter.entity',
            ];
          }
          $route->setOption('parameters', $parameters);
          $collection->add('react_routes.route.' . $key, $route);
          break;
      }
    }
  }

}
