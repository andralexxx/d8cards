<?php

namespace Drupal\dino_roar\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\dino_roar\Jurassic\RoarGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Route controller.
 */
class RoarController extends ControllerBase {

  /**
   * @var \Drupal\dino_roar\Jurassic\RoarGenerator
   */
  private $roarGenerator;

  /**
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  private $loggerFactoryService;

  /**
   * RoarController constructor.
   *
   * @param RoarGenerator $roarGenerator
   * @param LoggerChannelFactoryInterface $loggerFactoryService
   */
  public function __construct(RoarGenerator $roarGenerator, LoggerChannelFactoryInterface $loggerFactoryService) {
    $this->roarGenerator = $roarGenerator;
    $this->loggerFactoryService = $loggerFactoryService;
  }

  /**
   * Main route method.
   */
  public function roar($count) {
    $roar = $this->roarGenerator->getRoar($count);
    $this->loggerFactoryService->get('default')
      ->debug($roar);

    return [
      '#title' => $roar,
    ];
  }

  /**
   * @inheritDoc.
   */
  public static function create(ContainerInterface $container) {
    $roarGenerator = $container->get('dino_roar.roar_generator');
    $loggerFactory = $container->get('logger.factory');

    return new static($roarGenerator, $loggerFactory);
  }

}
