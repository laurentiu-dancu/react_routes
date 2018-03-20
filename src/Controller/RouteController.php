<?php

namespace Drupal\react_routes\Controller;

use Drupal\Core\Controller\ControllerBase;

class RouteController extends ControllerBase {

    /**
     * Renders a view as a React app.
     *
     * @param $viewId
     *   The view id.
     * @param $displayId
     *   The display id.
     *
     * @return array
     *   The render array.
     */
    public function renderView($viewId, $displayId) {
        $args = func_get_args();
        $build = call_user_func_array('views_embed_view', $args);
        $result = render($build);
        kint($args);

        return [
            '#theme' => 'view_render',
            '#props' => $result,
        ];
    }
}
