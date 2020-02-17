<?php

namespace Drupal\custom_cache\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'CustomCacheBlock' block.
 *
 * @Block(
 *  id = "custom_cache_block",
 *  admin_label = @Translation("Custom Cache Block"),
 * )
 */
class CustomCacheBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->entityTypeManager = $container->get('entity_type.manager');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $query = $this->entityTypeManager->getStorage('node')->getQuery();
    $nids = $query->condition('status', 1)
      ->sort('changed', 'DESC')
      ->range(0, 5)
      ->execute();

    $nodes = $this->entityTypeManager->getStorage('node')->loadMultiple($nids);

    $titles = [];
    foreach ($nodes as $node) {
      $titles['node:' . $node->id()] = $node->label();
    }

    return [
      'custom_cache_block' => [
        '#markup' => implode(' - ', $titles),
        '#cache' => [
//          'tags' => ['node_list'],
          'tags' => array_keys($titles),
        ],
      ],
    ];
  }

}
