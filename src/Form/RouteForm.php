<?php

namespace Drupal\react_routes\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\react_routes\Entity\Route;

class RouteForm extends EntityForm {

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form = parent::buildForm($form, $form_state);

        /* @var $route Route */
        $route = $this->entity;
        $options = $this->getViewOptions();
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

        $form['type'] = [
            '#title' => $this->t('Resource type'),
            '#type' => 'select',
            '#options' => [
                'rest' => $this->t('Rest'),
                'view' => $this->t('View'),
            ],
        ];

        $select_options = array_column($options, 'label');
        $form['options'] = [
            '#title' => $this->t('Rest View'),
            '#type' => 'select',
            '#options' => $select_options,
        ];

        $form['path'] = [
            '#title' => $this->t('Path'),
            '#type' => 'textfield',
            '#default_value' => $route->getPath(),
        ];

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $this->cleanValues($form_state);
        parent::submitForm($form, $form_state);
    }

    /**
     * Cleans the form values before submission.
     *
     * @param FormStateInterface $formState
     */
    protected function cleanValues(FormStateInterface $formState) {
        $values = $formState->getValues();
        $options = $formState->get('options');
        $values['options'] = $options[$values['options']];
        $formState->setValues($values);
    }

    /**
     * Gets the valid view options.
     *
     * @return array
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
                    $options[] = [
                        'label' => $view->label() . '/' . $display['display_title'],
                        'view' => $view->id(),
                        'display' => $display['id'],
                        'view_path' => $display['display_options']['path'],
                    ];
                }
            }
        }

        return $options;
    }
}
