<?php

namespace Drupal\custom_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;

/**
 * Provides a 'Movies' migrate source.
 *
 * @MigrateSource(
 *  id = "movies",
 *  source_module = "custom_migrate"
 * )
 */
class Movies extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    return $this->select('movies', 'd')
      ->fields('d', ['id', 'name', 'description'])
;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'id' => $this->t('Movie ID'),
      'name' => $this->t('Movie Name'),
      'description' => $this->t('Movie Description'),
      'genres' => $this->t('Movie Genres'),
    ];
    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'id' => [
        'type' => 'integer',
        'alias' => 'd',
      ],
    ];
  }

}
