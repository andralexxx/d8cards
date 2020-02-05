<?php

namespace Drupal\custom_content_entity_type;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Routing\AdminHtmlRouteProvider;

/**
 * Provides routes for Contact type entities.
 *
 * @see \Drupal\Core\Entity\Routing\AdminHtmlRouteProvider
 * @see \Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider
 */
class ContactTypeHtmlRouteProvider extends AdminHtmlRouteProvider {

  /**
   * {@inheritdoc}
   */
  public function getRoutes(EntityTypeInterface $entity_type) {

    /** @var \Symfony\Component\Routing\RouteCollection|\Symfony\Component\Routing\Route[] $collection */
    $collection = parent::getRoutes($entity_type);

    // Provide your custom entity routes here.
    return $collection;
  }

}
