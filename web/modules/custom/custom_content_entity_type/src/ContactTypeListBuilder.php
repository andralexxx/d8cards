<?php

namespace Drupal\custom_content_entity_type;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of Contact type entities.
 */
class ContactTypeListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Machine name');
    $header['label'] = $this->t('Contact type');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['id'] = $entity->id();
    $row['label'] = $entity->label();
    return $row + parent::buildRow($entity);
  }

}
