<?php

namespace Drupal\react_routes\ListBuilder;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\react_routes\Entity\Route;

/**
 * Class RoutesListBuilder.
 *
 * @package Drupal\react_routes\ListBuilder
 */
class RoutesListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('ID');
    $header['label'] = $this->t('Label');
    $header['path'] = $this->t('Path');

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity Route */
    $row['id'] = $entity->id();
    $row['label'] = $entity->label();
    $row['path'] = $entity->getPath();

    return $row + parent::buildRow($entity);
  }

}
