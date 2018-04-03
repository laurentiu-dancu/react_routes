<?php

namespace Drupal\react_routes\Entity;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\Annotation\ConfigEntityType;
use Drupal\Core\Entity\EntityInterface;

/**
 * Defines the route configuration.
 *
 * @ConfigEntityType(
 *   id = "react_route",
 *   label = @Translation("React Route"),
 *     admin_permission = "administer react routes",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *   },
 *   handlers = {
 *     "list_builder" = "Drupal\react_routes\ListBuilder\RoutesListBuilder",
 *     "form" = {
 *       "add" = "Drupal\react_routes\Form\RouteForm",
 *       "edit" = "Drupal\react_routes\Form\RouteForm"
 *     }
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "path",
 *     "type",
 *     "options",
 *   },
 *   links = {
 *     "edit-form" = "/admin/structure/react-routes/{id}/edit"
 *   }
 * )
 */
class Route extends ConfigEntityBase implements EntityInterface {
  /**
   * The unique ID of the route.
   *
   * @var string
   */
  protected $id;

  /**
   * The label of the route.
   *
   * @var string
   */
  protected $label;

  /**
   * The route path.
   *
   * @var string
   */
  protected $path;

  /**
   * The route arguments.
   *
   * @var string
   */
  protected $arguments;

  /**
   * The route type.
   *
   * @var string
   */
  protected $type;

  /**
   * The route options.
   *
   * @var array
   */
  protected $options;

  /**
   * Gets the ID.
   *
   * @return string
   *   The id.
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Sets the ID.
   *
   * @param string $id
   *   The id.
   *
   * @return Route
   *   This entity.
   */
  public function setId($id) {
    $this->id = $id;
    return $this;
  }

  /**
   * Gets the label.
   *
   * @return string
   *   The label.
   */
  public function getLabel() {
    return $this->label;
  }

  /**
   * Sets the label.
   *
   * @param string $label
   *   The label.
   *
   * @return Route
   *   This entity.
   */
  public function setLabel($label) {
    $this->label = $label;
    return $this;
  }

  /**
   * Gets the path.
   *
   * @return string
   *   The path.
   */
  public function getPath() {
    return $this->path;
  }

  /**
   * Sets the path.
   *
   * @param string $path
   *   The path.
   *
   * @return Route
   *   This entity.
   */
  public function setPath($path) {
    $this->path = $path;
    return $this;
  }

  /**
   * Gets the type.
   *
   * @return string
   *   The type.
   */
  public function getType() {
    return $this->type;
  }

  /**
   * Sets the type.
   *
   * @param string $type
   *   The type.
   *
   * @return Route
   *   This route.
   */
  public function setType($type) {
    $this->type = $type;
    return $this;
  }

  /**
   * Sets the options.
   *
   * @return array
   *   The options.
   */
  public function getOptions() {
    return $this->options;
  }

  /**
   * Sets the options.
   *
   * @param array $options
   *   The options.
   *
   * @return Route
   *   This entity.
   */
  public function setOptions(array $options) {
    $this->options = $options;
    return $this;
  }

}
