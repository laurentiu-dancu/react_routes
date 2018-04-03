<?php

namespace Drupal\react_routes\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Render\RenderContext;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\rest\Entity\RestResourceConfig;
use Drupal\rest\Plugin\rest\resource\EntityResource;
use Drupal\rest\RequestHandler;
use Drupal\rest\ResourceResponseInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class RouteController.
 *
 * @package Drupal\react_routes\Controller
 */
class RouteController extends ControllerBase {

  /**
   * Renders a view as a React app.
   *
   * @param string $viewId
   *   The view id.
   * @param string $displayId
   *   The display id.
   * @param RouteMatchInterface $route_match
   *   The route match object.
   *
   * @return array
   *   The render array.
   */
  public function renderView($viewId, $displayId, RouteMatchInterface $route_match) {
    $parameters = $route_match->getParameters()->all();
    $build = call_user_func_array('views_embed_view', $parameters);
    $result = render($build);

    return [
      '#theme' => 'react_render',
      '#props' => $result,
    ];
  }

  /**
   * Renders a Rest resource.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match object.
   * @param \Drupal\rest\Entity\RestResourceConfig $_rest_resource_config
   *   The rest resource config object.
   *
   * @return array
   *   A render array.
   */
  public function renderResource(RouteMatchInterface $route_match, RestResourceConfig $_rest_resource_config) {
    $parameters = $route_match->getParameters()->all();
    $plugin = $_rest_resource_config->getResourcePlugin();
    $response = call_user_func_array([$plugin, 'get'], $parameters);
    $output = $this->renderResourceResponse($response);

    return [
      '#theme' => 'react_render',
      '#props' => $output,
    ];
  }

  /**
   * Converts a resource response to string.
   *
   * @param \Drupal\rest\ResourceResponseInterface $response
   *   The response.
   * @param string $format
   *   The serialize format.
   *
   * @return string
   *   The serialized string.
   */
  protected function renderResourceResponse(ResourceResponseInterface $response, $format = 'json') {
    $serializer = \Drupal::service('serializer');
    $renderer = \Drupal::service('renderer');
    $data = $response->getResponseData();

    $context = new RenderContext();
    $output = $renderer->executeInRenderContext($context, function () use ($serializer, $data, $format) {
      return $serializer->serialize($data, $format);
    });

    return $output;
  }

}
