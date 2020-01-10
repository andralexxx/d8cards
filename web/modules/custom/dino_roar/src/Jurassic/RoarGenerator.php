<?php

namespace Drupal\dino_roar\Jurassic;

use Drupal\Core\KeyValueStore\KeyValueFactoryInterface;

class RoarGenerator {

  /**
   * @var \Drupal\Core\KeyValueStore\KeyValueFactoryInterface
   */
  private $keyValueFactory;

  private $useCache;

  /**
   * RoarGenerator constructor.
   */
  public function __construct(KeyValueFactoryInterface $keyValueFactory, $useCache) {
    $this->keyValueFactory = $keyValueFactory;
    $this->useCache = $useCache;
  }

  /**
   * Generates Roar string depending on input length.
   *
   * @param int $length
   *   The length of the string.
   *
   * @return string
   *   The Roar string.
   */
  public function getRoar($length) {
    $key = 'roar_' . $length;
    $store = $this->keyValueFactory->get('dino');

    if ($this->useCache && $store->has($key)) {
      return $store->get($key);
    }

    sleep(2);
    $string = 'R' . str_repeat('O', $length) . 'AR!';

    if ($this->useCache) {
      $store->set($key, $string);
    }

    return $string;
  }

}
