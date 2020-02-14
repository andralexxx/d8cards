<?php

namespace Drupal\custom_events\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\custom_events\Event\SimplePageEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SimpleController.
 */
class SimpleController extends ControllerBase {

  /**
   * Symfony\Component\EventDispatcher\EventDispatcherInterface definition.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $dispatcher;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->dispatcher = $container->get('event_dispatcher');
    return $instance;
  }

  /**
   * Simple.
   *
   * @return string
   *   Return Hello string.
   */
  public function simple() {
    $event = new SimplePageEvent();
    $this->dispatcher->dispatch($event::EVENT_NAME, $event);

    return [
      '#markup' => '<p>' . $this->t('Simple page: The quick brown fox jumps over the lazy dog.') . '</p>',
    ];
  }

}
