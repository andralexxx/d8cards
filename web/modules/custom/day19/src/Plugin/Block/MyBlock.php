<?php

/**
 * @file
 * Contains \Drupal\day19\Plugin\Block\MyBlock.
 */

namespace Drupal\day19\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'MyBlock' block.
 *
 * @Block(
 *  id = "my_block",
 *  admin_label = @Translation("My block"),
 * )
 */
class MyBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $some_array = [
      1 => ['a' => 'mango', 'b' => 'guava', 'c' => 'jack fruit'],
      2 => ['d' => 'banana', 'e' => 'orange', 'f' => 'pine apple'],
      3 => ['g' => 'water melon', 'h' => 'brinjal', 'i' => 'potato'],
    ];

    return [
      '#theme' => 'day19_twig_test',
      '#title' => $this->t('Test Title'),
      '#var1' => $this->t('Test Description'),
      '#var2' => $some_array,
      '#classes' => ['class1', 'class2'],
      '#myclasscount' => 5,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return 0;
  }

}
