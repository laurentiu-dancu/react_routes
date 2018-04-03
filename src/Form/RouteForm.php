<?php

namespace Drupal\react_routes\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\react_routes\Entity\Route;
use Drupal\rest\Plugin\rest\resource\EntityResource;

/**
 * Class RouteForm.
 *
 * @package Drupal\react_routes\Form
 */
class RouteForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    /* @var $route Route */
    $route = $this->entity;
    $view_options = $this->getViewOptions();
    $resource_options = $this->getResourceOptions();

    $options = array_merge($view_options, $resource_options);

    $form_state->set('options', $options);

    $form['label'] = [
      '#title' => t('Name'),
      '#type' => 'textfield',
      '#default_value' => $route->label(),
      '#description' => t('The human-readable name of this route.'),
      '#required' => TRUE,
      '#size' => 30,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $route->id(),
      '#maxlength' => EntityTypeInterface::BUNDLE_MAX_LENGTH,
      '#machine_name' => [
        'exists' => ['Drupal\react_routes\Entity\Route', 'load'],
        'source' => ['label'],
      ],
      '#description' => t('A unique machine-readable name for this route. It must only contain lowercase letters, numbers, and underscores.', [
        '%node-add' => t('Add content'),
      ]),
    ];

    $options = array_combine(array_keys($options), array_column($options, 'label'));
    $default_options = $route->getOptions();

    $form['options'] = [
      '#title' => $this->t('Rest View'),
      '#type' => 'radios',
      '#options' => $options,
      '#default_value' => isset($default_options['id']) ? $default_options['id'] : NULL,
      '#required' => TRUE,
    ];

    $form['path'] = [
      '#title' => $this->t('Path'),
      '#type' => 'textfield',
      '#default_value' => $route->getPath(),
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->cleanValues($form_state);
    parent::submitForm($form, $form_state);
  }

  /**
   * Cleans the form values before submission.
   *
   * @param FormStateInterface $formState
   *   The form state object.
   */
  protected function cleanValues(FormStateInterface $formState) {
    $values = $formState->getValues();
    $options = $formState->get('options');
    $values['options'] = $options[$values['options']];
    $values['type'] = $values['options']['type'];
    $formState->setValues($values);
  }

  /**
   * Gets the valid view options.
   *
   * @return array
   *   The options.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  protected function getViewOptions() {
    $view_storage = $this->entityTypeManager->getStorage('view');
    $views = $view_storage->loadMultiple();

    $options = [];
    foreach ($views as $view) {
      $display_list = $view->get('display');
      foreach ($display_list as $display) {
        if ($display['display_plugin'] == 'rest_export') {
          $id = 'view.' . $view->id() . '.' . $display['id'];
          $options[$id] = [
            'id' => $id,
            'type' => 'view',
            'label' => $view->label() . '/' . $display['display_title'] . ' (' . $display['display_options']['path'] . ')',
            'view' => $view->id(),
            'display' => $display['id'],
            'view_path' => $display['display_options']['path'],
          ];
        }
      }
    }

    return $options;
  }

  /**
   * Gets the valid resource options.
   *
   * @return array
   *   The options.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  protected function getResourceOptions() {
    $rest_storage = $this->entityTypeManager->getStorage('rest_resource_config');
    $config = $rest_storage->loadMultiple();

    $options = [];
    foreach ($config as $entity) {
      $plugin = $entity->getResourcePlugin();
      if ($plugin instanceof EntityResource) {
        $definition = $plugin->getPluginDefinition();
        $id = 'resource.' . $definition['id'];
        $options[$id] = [
          'id' => $id,
          'type' => 'rest',
          'label' => $definition['label'] . ' resource (' . $definition['uri_paths']['canonical'] . ')',
          'parameters' => [
            $definition['entity_type'] => $definition['id'],
          ],
          'resource_id' => $entity->id(),
        ];
      }
    }

    return $options;
  }

}
